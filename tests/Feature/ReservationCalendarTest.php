<?php

namespace Tests\Feature;

use App\Mail\ConfirmReservation;
use App\Models\Event\Event;
use App\Models\Reservation\Reservation;
use App\Models\User;
use App\Services\Event\EventService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class ReservationCalendarTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'reservation_test',
            'database.connections.reservation_test' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => false,
            ],
            'jwt.secret' => str_repeat('test-key', 8),
        ]);
        Mail::fake();

        // Exercise the real controllers and services without the unrelated legacy migrations.
        foreach ([
            'reservations' => ['firstname', 'lastname', 'cf', 'p_iva', 'ragione_sociale', 'email', 'phone',
                'number', 'delegated_firstname', 'delegated_lastname', 'message', 'date', 'duration', 'status',
                'parameter_id', 'client_id', 'refuse_reason', 'confirmation_token'],
            'clients' => ['firstname', 'lastname', 'company_name'],
            'ticket_clients' => ['firstname', 'lastname'],
            'events' => ['title', 'description', 'start_date', 'end_date', 'url', 'all_day', 'group_id',
                'client_id', 'ticket_client_id'],
        ] as $name => $columns) {
            Schema::create($name, function (Blueprint $table) use ($columns) {
                $table->id();
                foreach ($columns as $column) {
                    $table->text($column)->nullable();
                }
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
        $this->migration()->up();
        DB::table('clients')->insert(['id' => 1, 'company_name' => 'Urbania']);
    }

    private function migration()
    {
        return require database_path('migrations/2026_09_28_130000_add_reservation_id_to_events.php');
    }

    private function reservation(int $status = 2): Reservation
    {
        return Reservation::create([
            'firstname' => 'Mario', 'lastname' => 'Rossi', 'email' => 'customer@example.com',
            'phone' => '123456', 'message' => 'Tax appointment', 'date' => '2026-10-05 10:00:00',
            'duration' => 30, 'status' => $status, 'client_id' => 1,
            'confirmation_token' => $status === 2 ? str_repeat('a', 32) : null,
        ]);
    }

    private function event(Reservation $reservation, bool $linked = true): Event
    {
        $event = (new Event)->forceFill([
            'title' => 'Urbania | '.$reservation->firstname.' '.$reservation->lastname,
            'description' => $reservation->message, 'start_date' => $reservation->date,
            'end_date' => $reservation->date, 'url' => '', 'all_day' => 0,
            'group_id' => null, 'client_id' => 1, 'ticket_client_id' => null,
            'reservation_id' => $linked ? $reservation->id : null,
        ]);
        $event->save();

        return $event;
    }

    private function control(Reservation $reservation, int|string $status)
    {
        return $this->putJson('/api/v1/client-reservations/control', [
            'reservationId' => $reservation->id, 'token' => $reservation->confirmation_token, 'status' => $status,
        ]);
    }

    private function adminUpdate(Reservation $reservation, array $overrides = [])
    {
        $this->actingAs((new User)->forceFill(['id' => 1]), 'api');

        return $this->putJson('/api/v1/reservations/update', array_replace([
            'reservationId' => $reservation->id, 'firstname' => $reservation->firstname,
            'lastname' => $reservation->lastname, 'email' => $reservation->email,
            'phone' => $reservation->phone, 'message' => $reservation->message,
            'date' => $reservation->date, 'duration' => 30, 'status' => 2, 'clientId' => 1,
        ], $overrides));
    }

    public function test_customer_rejection_removes_only_linked_event_from_calendar_and_is_repeatable(): void
    {
        $reservation = $this->reservation();
        $event = $this->event($reservation);
        $unrelated = $this->event($reservation, false);
        $this->control($reservation, '0')->assertOk();
        $this->control($reservation, 0)->assertOk();

        $this->assertSame('0', $reservation->fresh()->status);
        $this->assertSoftDeleted('events', ['id' => $event->id]);
        $this->assertNotNull($unrelated->fresh());
        $this->assertSame(2, Event::withTrashed()->count());
        $visible = app(EventService::class)->getAllEvents(new Request);
        $this->assertEquals([$unrelated->id], $visible->pluck('id')->all());
        Mail::assertNothingSent();
    }

    public function test_customer_confirmation_keeps_same_event_and_can_restore_it_after_rejection(): void
    {
        $reservation = $this->reservation();
        $event = $this->event($reservation);
        $this->control($reservation, '2')->assertOk();
        $this->control($reservation, 2)->assertOk();
        $this->control($reservation, 0)->assertOk();
        $this->control($reservation, 2)->assertOk();

        $this->assertSame('2', $reservation->fresh()->status);
        $this->assertNotNull(Event::find($event->id));
        $this->assertSame(1, Event::withTrashed()->count());
        Mail::assertNothingSent();
    }

    public function test_admin_confirmation_creates_once_and_updates_same_event(): void
    {
        $reservation = $this->reservation(1);
        $this->adminUpdate($reservation)->assertOk();
        $event = Event::sole();
        $token = $reservation->fresh()->confirmation_token;
        $this->adminUpdate($reservation)->assertOk();
        $this->adminUpdate($reservation, ['date' => '2026-10-06 11:00:00'])->assertOk();

        $this->assertSame(1, Event::withTrashed()->count());
        $this->assertSame($reservation->id, $event->fresh()->reservation_id);
        $this->assertSame('2026-10-06 11:00:00', $event->fresh()->start_date);
        $this->assertSame($token, $reservation->fresh()->confirmation_token);
        Mail::assertSent(ConfirmReservation::class, 3);
    }

    public function test_admin_rejection_removes_event_and_reacceptance_reuses_it(): void
    {
        $reservation = $this->reservation();
        $event = $this->event($reservation);
        $this->adminUpdate($reservation, ['status' => 0])->assertOk();
        $this->assertSoftDeleted('events', ['id' => $event->id]);
        $this->adminUpdate($reservation)->assertOk();
        $this->assertNotNull(Event::find($event->id));
        $this->assertSame(1, Event::withTrashed()->count());
    }

    public function test_unique_legacy_event_is_linked_before_customer_rejection(): void
    {
        $reservation = $this->reservation();
        $event = $this->event($reservation, false);
        $this->control($reservation, 0)->assertOk();
        $this->assertSoftDeleted('events', ['id' => $event->id]);
        $this->assertEquals($reservation->id, DB::table('events')->where('id', $event->id)->value('reservation_id'));
    }

    public function test_legacy_event_is_resolved_before_admin_changes_date_and_name(): void
    {
        $reservation = $this->reservation();
        $event = $this->event($reservation, false);
        $this->adminUpdate($reservation, ['firstname' => 'Luigi', 'date' => '2026-10-06 10:00:00'])->assertOk();
        $this->assertSame('Urbania | Luigi Rossi', $event->fresh()->title);
        $this->assertSame('2026-10-06 10:00:00', $event->fresh()->start_date);
        $this->assertSame(1, Event::withTrashed()->count());
    }

    public function test_ambiguous_legacy_events_are_not_deleted_or_duplicated(): void
    {
        $reservation = $this->reservation();
        $this->event($reservation, false);
        $this->event($reservation, false);
        $this->control($reservation, 0)->assertStatus(409);
        $this->adminUpdate($reservation)->assertStatus(409);
        $this->assertSame('2', $reservation->fresh()->status);
        $this->assertSame(2, Event::count());
        $this->assertSame(0, Event::whereNotNull('reservation_id')->count());
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_shared_legacy_candidate_is_not_claimed(): void
    {
        $reservation = $this->reservation();
        $this->reservation()->forceFill(['ragione_sociale' => 'Different hidden company'])->save();
        $this->event($reservation, false);
        $this->control($reservation, 0)->assertStatus(409);
        $this->assertSame(1, Event::count());
    }

    public function test_an_event_claimed_by_another_reservation_is_not_deleted(): void
    {
        $reservation = $this->reservation();
        $other = $this->reservation();
        $other->firstname = 'Luigi';
        $other->save();
        $event = $this->event($reservation, false);
        $event->reservation_id = $other->id;
        $event->saveQuietly();
        $this->control($reservation, 0)->assertStatus(409);
        $this->assertNotNull(Event::find($event->id));
    }

    public function test_token_for_another_reservation_cannot_cancel_this_one(): void
    {
        $reservation = $this->reservation();
        $this->event($reservation);
        $other = $this->reservation();
        $other->confirmation_token = str_repeat('b', 32);
        $other->save();
        $this->putJson('/api/v1/client-reservations/control', [
            'reservationId' => $reservation->id, 'token' => $other->confirmation_token, 'status' => 0,
        ])->assertUnprocessable();
        $this->assertSame('2', $reservation->fresh()->status);
        $this->assertSame(1, Event::count());
    }

    public function test_admin_can_approve_a_pending_reservation_without_an_existing_client(): void
    {
        $reservation = $this->reservation(1);
        $reservation->client_id = null;
        $reservation->save();
        $this->adminUpdate($reservation)->assertOk();
        $this->assertSame($reservation->id, Event::sole()->reservation_id);
    }

    public function test_missing_legacy_match_requires_review_instead_of_silent_success(): void
    {
        $reservation = $this->reservation();
        $this->event($reservation, false)->forceFill(['title' => 'Manually changed title'])->saveQuietly();
        $this->control($reservation, 0)->assertStatus(409);
        $this->assertSame('2', $reservation->fresh()->status);
        $this->assertSame(1, Event::count());
    }

    public static function invalidControlData(): array
    {
        return [[['token' => null]], [['token' => '']], [['token' => 'wrong']],
            [['status' => 1]], [['status' => 3]], [['status' => null]], [['reservationId' => 999]]];
    }

    #[DataProvider('invalidControlData')]
    public function test_invalid_control_request_cannot_change_calendar(array $overrides): void
    {
        $reservation = $this->reservation();
        $this->event($reservation);
        $this->putJson('/api/v1/client-reservations/control', array_replace([
            'reservationId' => $reservation->id, 'token' => $reservation->confirmation_token, 'status' => 0,
        ], $overrides))->assertUnprocessable();
        $this->assertSame('2', $reservation->fresh()->status);
        $this->assertSame(1, Event::count());
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_mail_failure_rolls_back_rejection_and_event_deletion(): void
    {
        $reservation = $this->reservation();
        $event = $this->event($reservation, false);
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new TransportException('Unavailable'));
        $this->adminUpdate($reservation, ['status' => 0])->assertStatus(500);
        $this->assertSame('2', $reservation->fresh()->status);
        $this->assertNotNull(Event::find($event->id));
        $this->assertNull($event->fresh()->reservation_id);
    }

    public function test_migration_rollback_preserves_events(): void
    {
        $reservation = $this->reservation();
        $event = $this->event($reservation);
        $this->migration()->down();
        $this->assertFalse(Schema::hasColumn('events', 'reservation_id'));
        $this->assertNotNull(Event::find($event->id));
    }
}
