<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PrivacyAcceptTest extends TestCase
{
    public function test_migration_compiles_as_tinyint_default_zero_for_mysql(): void
    {
        $connection = new \Illuminate\Database\MySqlConnection(fn () => null);
        Schema::swap($connection->getSchemaBuilder());
        $migration = require database_path('migrations/2026_10_14_120000_add_privacy_accept_to_tickets_and_reservations.php');
        $queries = $connection->pretend(fn () => $migration->up());
        $this->assertCount(4, $queries);
        foreach ($queries as $query) {
            $this->assertStringContainsString('`privacy_accept` tinyint not null default', $query['query']);
            $this->assertStringContainsString("'0'", $query['query']);
        }
    }

    public function test_migration_defaults_existing_and_new_rows_to_zero_and_rolls_back(): void
    {
        config([
            'database.default' => 'privacy_test',
            'database.connections.privacy_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        ]);
        $tables = ['client_outer_tickets', 'tickets', 'reservations', 'events'];
        foreach ($tables as $name) {
            Schema::create($name, fn (Blueprint $table) => $table->id());
            DB::table($name)->insert(['id' => 1]);
        }
        $migration = require database_path('migrations/2026_10_14_120000_add_privacy_accept_to_tickets_and_reservations.php');
        $migration->up();
        foreach ($tables as $name) {
            DB::table($name)->insert(['id' => 2]);
            $this->assertSame([0, 0], DB::table($name)->orderBy('id')->pluck('privacy_accept')->all());
            DB::table($name)->where('id', 1)->update(['privacy_accept' => 1]);
            $this->assertSame(1, DB::table($name)->where('id', 1)->value('privacy_accept'));
        }
        $migration->down();
        foreach ($tables as $name) {
            $this->assertFalse(Schema::hasColumn($name, 'privacy_accept'));
            $this->assertSame(2, DB::table($name)->count());
        }
    }

    public static function requestClasses(): array
    {
        return array_map(fn ($name) => ['App\\Http\\Requests\\'.$name], [
            'ClientOuterTicket\\CreateClientOuterTicketRequest', 'ClientOuterTicket\\UpdateClientOuterTicketRequest',
            'ClientOuterTicket\\SollecitoRequest',
            'Reservation\\CreateReservationRequest', 'Reservation\\UpdateReservationRequest',
            'Ticket\\CreateTicketRequest', 'Ticket\\UpdateTicketRequest',
            'Event\\CreateEventRequest', 'Event\\UpdateEventRequest',
        ]);
    }

    #[DataProvider('requestClasses')]
    public function test_privacy_contract_accepts_zero_one_and_omission_but_rejects_invalid_values(string $class): void
    {
        $rules = (new $class)->rules();
        $this->assertArrayHasKey('privacyAccept', $rules);
        $privacyRule = ['privacyAccept' => $rules['privacyAccept']];
        foreach ([[], ['privacyAccept' => 0], ['privacyAccept' => 1], ['privacyAccept' => '0'], ['privacyAccept' => '1']] as $data) {
            $this->assertTrue(Validator::make($data, $privacyRule)->passes());
        }
        foreach ([null, '', -1, 2, 'yes', 'false', [], 0.5] as $value) {
            $this->assertTrue(Validator::make(['privacyAccept' => $value], $privacyRule)->fails());
        }
    }
}
