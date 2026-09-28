<?php

namespace Tests\Feature;

use App\Mail\OuterTicketCreated;
use App\Mail\OuterTicketRejected;
use App\Models\ClientOuterTicket;
use App\Models\User;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class OuterTicketDecisionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'outer_ticket_test',
            'database.connections.outer_ticket_test' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => false,
            ],
            'jwt.secret' => str_repeat('test-key', 8),
        ]);
        $this->actingAs((new User)->forceFill(['id' => 1]), 'api');
        Storage::fake('uploads');
        Mail::fake();

        // Isolate this workflow from unrelated legacy migrations and real application data.
        Schema::create('client_outer_tickets', function (Blueprint $table) {
            $table->id();
            $table->boolean('accept_status')->nullable();
            foreach (['number', 'firstname', 'lastname', 'cf', 'p_iva', 'ragione_sociale', 'email', 'phone',
                'address', 'city', 'state', 'delegated_firstname', 'delegated_lastname', 'delegated_phone',
                'message', 'status', 'anno', 'service_id', 'istanza_parameter_id', 'delegated_role_id',
                'client_id', 'ticket_client_id', 'contract_id', 'contract_two_id', 'email_token', 'status_date',
                'notify_date', 'end_date', 'connect_type_id', 'esito', 'note', 'segnalazione', 'urgenza',
                'worker_id', 'closer_id', 'date', 'created_by', 'updated_by'] as $column) {
                $table->text($column)->nullable();
            }
            $table->timestamps();
        });
        $this->migration()->up();

        $tables = [
            'clients' => ['company_name'],
            'ticket_clients' => ['firstname', 'lastname', 'company_name', 'national_number'],
            'ticket_client_contacts' => ['ticket_client_id', 'phone_number', 'email'],
            'ticket_client_addresses' => ['ticket_client_id', 'address', 'city', 'state', 'postal_code'],
            'tickets' => ['client_id', 'contract_id', 'contract_id_2', 'service_id', 'worker_id', 'ticket_client_id',
                'ticket_number', 'notify_date', 'status', 'closer_id', 'end_date', 'connect_type_id', 'description',
                'esito', 'note', 'status_date', 'anno', 'tipologia_istanza', 'segnalazione', 'urgenza', 'email_token'],
        ];
        foreach ($tables as $name => $columns) {
            Schema::create($name, function (Blueprint $table) use ($columns) {
                $table->id();
                foreach ($columns as $column) {
                    $table->text($column)->nullable();
                }
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    private function migration()
    {
        return require database_path('migrations/2026_09_28_120000_add_rejection_reason_to_client_outer_tickets.php');
    }

    private function ticket(?int $decision = 0): ClientOuterTicket
    {
        $ticket = new ClientOuterTicket;
        $ticket->forceFill([
            'firstname' => 'Mario', 'lastname' => 'Rossi', 'cf' => 'CLIENT123',
            'email' => 'customer@example.com', 'message' => 'Original request',
            'status' => '3', 'accept_status' => $decision,
        ])->save();

        return $ticket;
    }

    private function fullUpdate(ClientOuterTicket $ticket): array
    {
        return [
            'clientOuterTicketId' => $ticket->id,
            'firstname' => 'Mario', 'lastname' => 'Rossi', 'cf' => 'CLIENT123',
            'ragioneSociale' => '', 'email' => 'customer@example.com',
            'phone' => '', 'address' => '', 'message' => 'Original request',
            'status' => 0, 'clientId' => 1, 'ticketClientId' => 0,
        ];
    }

    public function test_rejection_saves_reason_sends_email_and_preserves_original_data(): void
    {
        $ticket = $this->ticket();
        $reason = "Missing document\nPlease include proof of payment.";
        $this->putJson('/api/v1/outer-tickets/update', [
            'clientOuterTicketId' => $ticket->id, 'acceptStatus' => 2, 'rejectionReason' => $reason,
        ])->assertOk();

        $ticket->refresh();
        $this->assertSame(2, $ticket->accept_status);
        $this->assertSame($reason, $ticket->rejection_reason);
        $this->assertSame('Mario', $ticket->firstname);
        $this->assertSame('Original request', $ticket->message);
        $this->assertSame('3', $ticket->status);
        $this->assertSame(0, DB::table('tickets')->count());
        $this->assertSame(0, DB::table('ticket_clients')->count());
        Mail::assertSent(OuterTicketRejected::class, fn ($mail) => $mail->hasTo('customer@example.com')
            && $mail->ticket->rejection_reason === $reason);
        Mail::assertSentCount(1);

        $this->getJson('/api/v1/outer-tickets/edit?clientOuterTicketId='.$ticket->id)
            ->assertOk()->assertJsonPath('rejectionReason', $reason)->assertJsonPath('acceptStatus', 2);
        $this->getJson('/api/v1/outer-tickets?isProcessed=1')
            ->assertOk()->assertJsonPath('clientOuterTickets.0.rejectionReason', $reason)
            ->assertJsonPath('clientOuterTickets.0.acceptStatus', 2);
    }

    public static function invalidReasons(): array
    {
        return [[null], [''], ['   '], [123], [str_repeat('a', 5001)]];
    }

    #[DataProvider('invalidReasons')]
    public function test_rejection_requires_nonempty_text_reason($reason): void
    {
        $ticket = $this->ticket();
        $this->putJson('/api/v1/outer-tickets/update', [
            'clientOuterTicketId' => $ticket->id, 'acceptStatus' => 2, 'rejectionReason' => $reason,
        ])->assertStatus(401)->assertJsonStructure(['message' => ['rejectionReason']]);

        $this->assertSame(0, $ticket->fresh()->accept_status);
        Mail::assertNothingSent();
    }

    public function test_rejection_does_not_resend_when_same_decision_is_saved(): void
    {
        $ticket = $this->ticket();
        $data = ['clientOuterTicketId' => $ticket->id, 'acceptStatus' => '2', 'rejectionReason' => 'Missing document'];
        $this->putJson('/api/v1/outer-tickets/update', $data)->assertOk();
        $this->putJson('/api/v1/outer-tickets/update', $data)->assertOk();
        $data['rejectionReason'] = 'Corrected reason';
        $this->putJson('/api/v1/outer-tickets/update', $data)->assertOk();

        $this->assertSame('Corrected reason', $ticket->fresh()->rejection_reason);
        Mail::assertSent(OuterTicketRejected::class, 1);
    }

    public function test_missing_decision_preserves_rejection_and_reason(): void
    {
        $ticket = $this->ticket(2);
        $ticket->rejection_reason = 'Existing reason';
        $ticket->save();
        $this->putJson('/api/v1/outer-tickets/update', $this->fullUpdate($ticket))->assertOk();

        $this->assertSame(2, $ticket->fresh()->accept_status);
        $this->assertSame('Existing reason', $ticket->fresh()->rejection_reason);
        Mail::assertNothingSent();
    }

    public function test_zero_does_not_send_or_convert_ticket(): void
    {
        $ticket = $this->ticket(null);
        $this->putJson('/api/v1/outer-tickets/update', $this->fullUpdate($ticket) + [
            'acceptStatus' => 0, 'rejectionReason' => 'Ignored outside rejection',
        ])->assertOk();

        $this->assertSame(0, $ticket->fresh()->accept_status);
        $this->assertNull($ticket->fresh()->rejection_reason);
        $this->assertSame(0, DB::table('tickets')->count());
        Mail::assertNothingSent();
    }

    public function test_acceptance_creates_internal_ticket_and_email_only_once(): void
    {
        $ticket = $this->ticket();
        $clientId = DB::table('ticket_clients')->insertGetId(['national_number' => 'CLIENT123']);
        Storage::disk('uploads')->put('outertickets/'.$ticket->id.'/signed.p7m', 'document');
        $data = array_replace($this->fullUpdate($ticket), ['acceptStatus' => 1, 'ticketClientId' => $clientId]);
        $this->putJson('/api/v1/outer-tickets/update', $data)->assertOk();
        $this->putJson('/api/v1/outer-tickets/update', $data)->assertOk();
        unset($data['acceptStatus']);
        $this->putJson('/api/v1/outer-tickets/update', $data)->assertOk();

        $this->assertSame(1, $ticket->fresh()->accept_status);
        $this->assertSame(1, DB::table('tickets')->count());
        $internalId = DB::table('tickets')->value('id');
        $this->assertSame('document', Storage::disk('uploads')->get('tickets/'.$internalId.'/signed.p7m'));
        Mail::assertSent(OuterTicketCreated::class, fn ($mail) => $mail->hasTo('customer@example.com'));
        Mail::assertSentCount(1);
    }

    public static function listFilters(): array
    {
        return [['', [0, null]], ['?isProcessed=0', [0, null]], ['?isProcessed=1', [1, 2]]];
    }

    #[DataProvider('listFilters')]
    public function test_list_filter_includes_correct_decisions_and_pagination(string $query, array $expected): void
    {
        foreach ([0, null, 1, 2] as $decision) {
            $this->ticket($decision);
        }
        $response = $this->getJson('/api/v1/outer-tickets'.$query)->assertOk();
        $this->assertEqualsCanonicalizing($expected, array_column($response->json('clientOuterTickets'), 'acceptStatus'));
        $response->assertJsonPath('pagination.total', 2);

        $separator = $query === '' ? '?' : '&';
        $this->getJson('/api/v1/outer-tickets'.$query.$separator.'pageSize=1&page=2')
            ->assertOk()->assertJsonCount(1, 'clientOuterTickets')
            ->assertJsonPath('pagination.total', 2)->assertJsonPath('pagination.total_pages', 2);
    }

    public function test_invalid_filter_and_decision_are_rejected(): void
    {
        $this->getJson('/api/v1/outer-tickets?isProcessed=2')->assertUnprocessable();
        $ticket = $this->ticket();
        $this->putJson('/api/v1/outer-tickets/update', $this->fullUpdate($ticket) + ['acceptStatus' => 3])
            ->assertStatus(401)->assertJsonStructure(['message' => ['acceptStatus']]);
        Mail::assertNothingSent();
    }

    public function test_rejection_without_valid_email_returns_error_instead_of_silent_success(): void
    {
        $ticket = $this->ticket();
        $ticket->email = '';
        $ticket->save();
        $this->putJson('/api/v1/outer-tickets/update', [
            'clientOuterTicketId' => $ticket->id, 'acceptStatus' => 2, 'rejectionReason' => 'Missing document',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertSame(0, $ticket->fresh()->accept_status);
        Mail::assertNothingSent();
    }

    public function test_mail_failure_rolls_back_decision_and_reason(): void
    {
        $ticket = $this->ticket();
        Mail::shouldReceive('to')->once()->with('customer@example.com')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new TransportException('Provider unavailable'));
        $this->putJson('/api/v1/outer-tickets/update', [
            'clientOuterTicketId' => $ticket->id, 'acceptStatus' => 2, 'rejectionReason' => 'Missing document',
        ])->assertStatus(500);

        $this->assertSame(0, $ticket->fresh()->accept_status);
        $this->assertNull($ticket->fresh()->rejection_reason);
    }

    public function test_mysql_migration_can_compile_without_extra_database_packages(): void
    {
        $connection = new MySqlConnection(fn () => null);
        $schema = $connection->getSchemaBuilder();
        $queries = $connection->pretend(function () use ($schema) {
            $schema->table('client_outer_tickets', function (Blueprint $table) {
                $table->unsignedTinyInteger('accept_status')->nullable()->default(0)->change();
            });
        });
        $this->assertStringContainsString('tinyint unsigned null default', $queries[0]['query']);
    }

    public function test_migration_rollback_does_not_reclassify_rejected_tickets(): void
    {
        $ticket = $this->ticket(2);
        $this->migration()->down();
        $this->assertFalse(Schema::hasColumn('client_outer_tickets', 'rejection_reason'));
        $this->assertSame(2, $ticket->fresh()->accept_status);
    }
}
