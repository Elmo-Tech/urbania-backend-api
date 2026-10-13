<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FreeReservationScheduleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'availability_test',
            'database.connections.availability_test' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
            ],
            'jwt.secret' => str_repeat('test-key', 8),
        ]);
        $this->travelTo(now()->setDate(2026, 10, 13)->setTime(12, 0));
        Schema::create('reservation_schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->text('schedule');
            $table->softDeletes();
        });
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->integer('status');
            $table->dateTime('date');
            $table->string('email')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->softDeletes();
        });
        DB::table('clients')->insert(['id' => 29, 'company_name' => 'SOLAROLO RAINERIO']);
    }

    private function dedicated(string $date = '2026-10-22', string $hours = '09:00 - 10:00'): array
    {
        return ['type' => 'dedicated', 'date' => $date, 'day' => null,
            'availableTimes' => [$hours], 'appointmentTime' => '30', 'restEachTime' => '0'];
    }

    private function fixed(string $day = 'thursday', string $hours = '09:00 - 10:00'): array
    {
        return array_replace($this->dedicated(), ['type' => 'fixed', 'date' => null, 'day' => $day, 'availableTimes' => [$hours]]);
    }

    private function schedule(array $entries, int $clientId = 29, bool $deleted = false): void
    {
        DB::table('reservation_schedules')->insert([
            'client_id' => $clientId, 'schedule' => json_encode($entries),
            'deleted_at' => $deleted ? now() : null,
        ]);
    }

    private function listing(string $start = '2026-10-22', string $end = '2026-10-22', int $clientId = 29)
    {
        return $this->getJson('/api/v1/free-schedules?'.http_build_query([
            'clientId' => $clientId, 'startDate' => $start, 'endDate' => $end,
        ]));
    }

    private function check(string $date, string $time, int $clientId = 29)
    {
        return $this->getJson('/api/v1/free-schedules/check-availability?'.http_build_query([
            'clientId' => $clientId, 'date' => $date, 'time' => $time,
        ]));
    }

    public static function municipalitySchedules(): array
    {
        return [
            'Solarolo separate records' => [29, '09:00 - 13:00', '09:00', 24, false],
            'Solarolo single record' => [29, '09:00 - 13:00', '09:00', 24, true],
            'Voltido separate records' => [30, '15:00 - 17:00', '15:00', 12, false],
            'Voltido single record' => [30, '15:00 - 17:00', '15:00', 12, true],
        ];
    }

    #[DataProvider('municipalitySchedules')]
    public function test_all_dedicated_dates_are_returned_and_bookable(int $clientId, string $hours, string $time, int $count, bool $singleRow): void
    {
        $entries = array_map(fn ($date) => array_replace($this->dedicated($date, $hours), ['appointmentTime' => '10']),
            ['2026-10-22', '2026-11-26']);
        foreach ($singleRow ? [$entries] : [[$entries[0]], [$entries[1]]] as $row) {
            $this->schedule($row, $clientId);
        }

        $this->listing('2026-10-01', '2026-11-30', $clientId)->assertOk()->assertJsonCount(2)
            ->assertJsonPath('0.date', '2026-10-22')->assertJsonPath('1.date', '2026-11-26')
            ->assertJsonCount($count, '0.times')->assertJsonCount($count, '1.times');
        $this->listing('2026-11-01', '2026-11-30', $clientId)->assertOk()->assertJsonCount(1)
            ->assertJsonPath('0.date', '2026-11-26');
        foreach (['2026-10-22', '2026-11-26'] as $date) {
            $this->check($date, $time, $clientId)->assertOk()->assertJsonPath('available', true);
        }
        $this->listing('2026-11-27', '2026-11-30', $clientId)->assertExactJson([]);
        $this->check('2026-11-27', $time, $clientId)->assertJsonPath('available', false);
    }

    public function test_fixed_weekdays_recur_across_months_and_multiple_records(): void
    {
        $this->schedule([$this->fixed('thursday')]);
        $this->schedule([$this->fixed('friday', '15:00 - 16:00')]);
        $this->listing('2026-10-22', '2026-11-06')->assertOk()->assertJsonCount(6)
            ->assertJsonPath('0.date', '2026-10-22')->assertJsonPath('1.date', '2026-10-23')
            ->assertJsonPath('4.date', '2026-11-05')->assertJsonPath('5.date', '2026-11-06');
        $this->check('2026-11-05', '09:00')->assertJsonPath('available', true);
        $this->check('2026-11-06', '15:30')->assertJsonPath('available', true);
        $this->check('2026-11-07', '09:00')->assertJsonPath('available', false);
    }

    public function test_fixed_and_dedicated_entries_combine_without_duplicates_in_time_order(): void
    {
        $this->schedule([$this->dedicated('2026-10-22', '10:00 - 11:00'), $this->fixed()]);
        $this->schedule([$this->fixed('thursday', '09:30 - 10:30'), $this->dedicated('2026-10-22', '11:00 - 12:00')]);
        $times = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30'];
        $this->listing()->assertExactJson([['date' => '2026-10-22', 'day' => 'Thursday',
            'times' => array_map(fn ($time) => ['time' => $time], $times)]]);
        foreach ($times as $time) {
            $this->check('2026-10-22', $time)->assertJsonPath('available', true);
        }
        $this->listing('2026-10-29', '2026-10-29')->assertJsonCount(3, '0.times');
        $this->check('2026-10-29', '11:00')->assertJsonPath('available', false);
    }

    public function test_slot_duration_breaks_and_end_boundary_are_preserved(): void
    {
        $this->schedule([array_replace($this->fixed(), [
            'availableTimes' => ['09:00 - 10:40', '15:00 - 16:00'], 'restEachTime' => '15',
        ])]);
        $response = $this->listing()->assertOk();
        $this->assertSame(['09:00', '09:45', '10:30', '15:00', '15:45'], array_column($response->json('0.times'), 'time'));
        $this->check('2026-10-22', '09:30')->assertJsonPath('available', false);
        $this->check('2026-10-22', '10:30')->assertJsonPath('available', true);
        $this->check('2026-10-22', '16:00')->assertJsonPath('available', false);
    }

    public function test_reservations_block_slots_and_existing_draft_cleanup_is_preserved(): void
    {
        $this->schedule([$this->fixed('thursday', '09:00 - 13:00')]);
        $this->schedule([$this->dedicated('2026-10-22', '09:00 - 13:00')]);
        foreach ([
            ['09:00', 2, 'customer@example.test', false, 20, 29],
            ['09:30', 1, 'customer@example.test', false, 20, 29],
            ['10:00', 0, null, false, 5, 29],
            ['10:30', 0, null, false, 20, 29],
            ['11:00', 0, '', false, 20, 29],
            ['11:30', 0, 'customer@example.test', false, 20, 29],
            ['12:00', 2, 'customer@example.test', true, 20, 29],
            ['12:30', 2, 'customer@example.test', false, 20, 30],
        ] as [$time, $status, $email, $deleted, $age, $clientId]) {
            DB::table('reservations')->insert(['client_id' => $clientId, 'date' => '2026-10-22 '.$time,
                'status' => $status, 'email' => $email, 'deleted_at' => $deleted ? now() : null,
                'created_at' => now()->subMinutes($age), 'updated_at' => now()]);
        }
        // Check cleanup through the single-slot endpoint as well as the calendar endpoint.
        $this->check('2026-10-22', '10:30')->assertJsonPath('available', true);
        $response = $this->listing()->assertOk();
        $this->assertSame(['10:30', '11:00', '12:00', '12:30'], array_column($response->json('0.times'), 'time'));
        $this->check('2026-10-22', '09:00')->assertJsonPath('available', false);
        $this->check('2026-10-22', '10:00')->assertJsonPath('available', false);
        $this->assertSame(3, DB::table('reservations')->whereNotNull('deleted_at')->count());
    }

    public function test_fully_booked_day_keeps_existing_empty_times_response(): void
    {
        $this->schedule([$this->dedicated('2026-10-22', '09:00 - 09:30')]);
        DB::table('reservations')->insert(['client_id' => 29, 'date' => '2026-10-22 09:00:00',
            'status' => 2, 'email' => 'customer@example.test', 'created_at' => now()]);
        $this->listing()->assertExactJson([['day' => 'Thursday', 'date' => '2026-10-22', 'times' => []]]);
        $this->check('2026-10-22', '09:00')->assertJsonPath('available', false);
    }

    public function test_deleted_schedules_and_other_clients_do_not_supply_availability(): void
    {
        $this->schedule([$this->dedicated()], 29, true);
        $this->schedule([$this->dedicated()], 30);
        $this->listing()->assertNotFound()->assertJsonPath('error', 'No schedule found for the client');
        $this->check('2026-10-22', '09:00')->assertNotFound();
        $this->schedule([$this->dedicated('2026-11-26')]);
        $this->listing()->assertExactJson([]);
        $this->check('2026-10-22', '09:00')->assertJsonPath('available', false);
    }

    public function test_client_name_lookup_still_supports_both_endpoints(): void
    {
        $this->schedule([$this->dedicated()]);
        $query = http_build_query(['clientName' => 'SOLAROLO RAINERIO', 'startDate' => '2026-10-22',
            'endDate' => '2026-10-22', 'date' => '2026-10-22', 'time' => '09:00']);
        $this->getJson('/api/v1/free-schedules?'.$query)->assertOk()->assertJsonCount(2, '0.times');
        $this->getJson('/api/v1/free-schedules/check-availability?'.$query)->assertOk()->assertJsonPath('available', true);
    }

    public function test_invalid_legacy_entries_do_not_hide_valid_schedules_or_stall_generation(): void
    {
        $this->schedule([
            array_replace($this->fixed(), ['appointmentTime' => '0']),
            array_replace($this->fixed(), ['restEachTime' => '-30']),
            array_replace($this->fixed(), ['appointmentTime' => 'Test']),
            array_replace($this->fixed(), ['day' => null]),
            array_replace($this->dedicated(), ['availableTimes' => ['Invalid Date - Invalid Date', '25:00 - 26:00', '10:00 - 09:00']]),
        ]);
        $this->schedule([$this->dedicated()]);
        $this->listing()->assertOk()->assertJsonCount(2, '0.times');
        $this->check('2026-10-22', '09:00')->assertJsonPath('available', true);
    }
}
