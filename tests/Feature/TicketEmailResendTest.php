<?php

namespace Tests\Feature;

use App\Mail\OuterTicketCreated;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Permission;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class TicketEmailResendTest extends TestCase
{
    private User $operator;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'ticket_email_test',
            'database.connections.ticket_email_test' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
            ],
            'cache.default' => 'array',
            'jwt.secret' => str_repeat('test-key', 8),
            'app.frontend_url' => 'https://frontend.example.test',
        ]);
        Mail::fake();
        Log::spy();

        require_once database_path('migrations/2023_08_01_124726_create_permission_tables.php');
        (new \CreatePermissionTables)->up();
        foreach (['updateTicket', 'allTicket', 'editTicket'] as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        foreach ([
            'users' => ['username', 'email'],
            'ticket_clients' => ['firstname', 'lastname', 'company_name', 'national_number'],
            'ticket_client_contacts' => ['ticket_client_id', 'email', 'phone_number'],
            'tickets' => ['client_outer_ticket_id', 'client_id', 'contract_id', 'contract_id_2', 'service_id',
                'worker_id', 'ticket_client_id', 'ticket_number', 'notify_date', 'status', 'closer_id',
                'end_date', 'connect_type_id', 'description', 'esito', 'note', 'status_date', 'anno',
                'tipologia_istanza', 'segnalazione', 'urgenza', 'email_token', 'privacy_accept'],
            'client_outer_tickets' => ['email', 'accept_status', 'ticket_client_id'],
            'events' => ['title', 'reservation_id'],
            'reservations' => ['email', 'status', 'confirmation_token'],
        ] as $name => $columns) {
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
        Schema::create('parameter_values', function (Blueprint $table) {
            $table->id();
            $table->string('description');
        });
        DB::table('parameter_values')->insert(['id' => 91, 'description' => '0']);
        DB::table('users')->insert(['id' => 1, 'username' => 'operator']);
        $this->operator = User::findOrFail(1);
        $this->operator->givePermissionTo('updateTicket');
        $this->actingAs($this->operator, 'api');

        DB::table('ticket_clients')->insert(['id' => 8, 'firstname' => 'Mario', 'lastname' => 'Rossi']);
        DB::table('ticket_client_contacts')->insert([
            'id' => 9, 'ticket_client_id' => 8, 'email' => 'current@example.com', 'phone_number' => '123456',
        ]);
        DB::table('client_outer_tickets')->insert([
            'id' => 12, 'email' => 'original@example.com', 'accept_status' => 1, 'ticket_client_id' => 8,
        ]);
        DB::table('tickets')->insert([
            'id' => 42, 'client_outer_ticket_id' => 12, 'ticket_client_id' => 8,
            'ticket_number' => '42_2026', 'email_token' => 'original-customer-token', 'status' => 1,
            'description' => 'Original description', 'privacy_accept' => 1, 'updated_by' => 17,
            'created_at' => '2026-10-04 12:30:00', 'updated_at' => '2026-10-05 10:00:00',
        ]);
        DB::table('events')->insert(['id' => 1, 'title' => 'Existing event', 'reservation_id' => 1]);
        DB::table('reservations')->insert(['id' => 1, 'status' => 2, 'confirmation_token' => 'reservation-token']);
    }

    private function resend(array $data = [])
    {
        return $this->postJson('/api/v1/tickets/reconfirm-email', $data + ['ticketId' => 42]);
    }

    private function snapshot(): array
    {
        $snapshot = [];
        foreach (['tickets', 'client_outer_tickets', 'ticket_clients', 'ticket_client_contacts', 'events', 'reservations'] as $table) {
            $snapshot[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }

        return $snapshot;
    }

    public function test_resends_existing_acceptance_to_current_internal_contact_without_any_record_changes(): void
    {
        $before = $this->snapshot();
        $this->resend()->assertOk()->assertExactJson([
            'message' => 'Email reinviata correttamente.',
        ]);
        Mail::assertSent(OuterTicketCreated::class, function ($mail) {
            $html = $mail->render();

            return $mail->hasTo('current@example.com') && ! $mail->hasTo('original@example.com')
                && $mail->subject === 'Segnalazione accettata'
                && str_contains($html, '42_2026') && str_contains($html, '04/10/2026 12:30')
                && $mail->editUrl === 'https://frontend.example.test/sollecito?ticketId=42&token=original-customer-token';
        });
        Mail::assertSentCount(1);
        $this->assertSame($before, $this->snapshot());
        Log::shouldHaveReceived('info')->with('Ticket acceptance email resend accepted.', \Mockery::on(
            fn ($context) => $context['ticket_id'] === 42 && $context['user_id'] === 1
                && $context['recipient'] === 'current@example.com'
                && ! str_contains(json_encode($context), 'original-customer-token')
        ))->once();
    }

    public function test_internal_ticket_edit_changes_the_recipient_for_the_next_resend(): void
    {
        $token = Auth::guard('api')->login($this->operator);
        Auth::forgetGuards();
        $this->withToken($token)->putJson('/api/v1/tickets/update', [
            'ticketId' => 42, 'ticketClientId' => 8, 'firstname' => 'Mario', 'lastname' => 'Rossi',
            'contactId' => 9, 'email' => 'edited@example.com', 'phoneNumber' => '123456',
            'clientId' => 1, 'contractId' => '18##16', 'serviceId' => 27, 'workerId' => '',
            'notifyDate' => null, 'status' => 1, 'description' => 'Edited internally',
            'connectTypeId' => null, 'note' => null, 'anno' => ['2026'], 'urgenza' => 91,
        ])->assertOk();
        $this->assertDatabaseHas('client_outer_tickets', ['id' => 12, 'email' => 'original@example.com']);
        $this->assertDatabaseHas('ticket_client_contacts', ['id' => 9, 'email' => 'edited@example.com']);
        $before = $this->snapshot();

        $this->resend()->assertOk()->assertExactJson(['message' => 'Email reinviata correttamente.']);
        Mail::assertSent(OuterTicketCreated::class, fn ($mail) => $mail->hasTo('edited@example.com'));
        Mail::assertSentCount(1);
        $this->assertSame($before, $this->snapshot());
    }

    public function test_request_cannot_override_recipient_content_or_token(): void
    {
        $before = $this->snapshot();
        $this->resend([
            'email' => 'override@example.com', 'ticketClientId' => 99, 'contactId' => 99,
            'subject' => 'Other subject', 'content' => 'Other content', 'token' => 'other-token',
        ])->assertOk()->assertExactJson(['message' => 'Email reinviata correttamente.']);
        Mail::assertSent(OuterTicketCreated::class, fn ($mail) => $mail->hasTo('current@example.com')
            && $mail->ticket->email_token === 'original-customer-token');
        $this->assertSame($before, $this->snapshot());
    }

    public function test_legacy_converted_ticket_with_token_does_not_need_external_record_or_link(): void
    {
        DB::table('tickets')->where('id', 42)->update(['client_outer_ticket_id' => null]);
        Schema::drop('client_outer_tickets');
        $this->resend()->assertOk();
        Mail::assertSent(OuterTicketCreated::class, 1);
    }

    #[DataProvider('ticketStatuses')]
    public function test_resend_preserves_active_closed_and_suspended_statuses(int $status): void
    {
        DB::table('tickets')->where('id', 42)->update(['status' => $status]);
        $before = $this->snapshot();
        $this->resend()->assertOk();
        $this->assertSame($before, $this->snapshot());
    }

    public static function ticketStatuses(): array
    {
        return [[1], [2], [3]];
    }

    #[DataProvider('invalidIds')]
    public function test_invalid_ticket_id_returns_validation_errors(array $payload): void
    {
        $this->postJson('/api/v1/tickets/reconfirm-email', $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('ticketId');
        Mail::assertNothingSent();
    }

    public static function invalidIds(): array
    {
        return [[[]], [['ticketId' => null]], [['ticketId' => 0]], [['ticketId' => -1]],
            [['ticketId' => 'abc']], [['ticketId' => [42]]], [['ticketId' => 1.5]]];
    }

    public function test_missing_and_deleted_tickets_are_not_sent(): void
    {
        $this->resend(['ticketId' => 999])->assertNotFound()->assertJsonPath('code', 'ticket_not_found');
        DB::table('tickets')->where('id', 42)->update(['deleted_at' => now()]);
        $this->resend()->assertNotFound()->assertJsonPath('code', 'ticket_not_found');
        Mail::assertNothingSent();
    }

    public function test_missing_or_blank_token_does_not_generate_one_or_send_email(): void
    {
        foreach ([null, '', '   '] as $token) {
            DB::table('tickets')->where('id', 42)->update(['email_token' => $token]);
            $before = $this->snapshot();
            $this->resend()->assertStatus(409)->assertJsonPath('code', 'ticket_email_unavailable');
            $this->assertSame($before, $this->snapshot());
        }
        Mail::assertNothingSent();
    }

    #[DataProvider('invalidRecipients')]
    public function test_missing_or_invalid_internal_email_never_falls_back_to_external_email(?string $email): void
    {
        DB::table('ticket_client_contacts')->where('id', 9)->update(['email' => $email]);
        // Additional contacts must not silently replace the selected first contact.
        DB::table('ticket_client_contacts')->insert(['id' => 10, 'ticket_client_id' => 8, 'email' => 'other@example.com']);
        $before = $this->snapshot();
        $this->resend()->assertUnprocessable()->assertJsonPath('code', 'ticket_email_invalid');
        Mail::assertNothingSent();
        $this->assertSame($before, $this->snapshot());
    }

    public static function invalidRecipients(): array
    {
        return [[null], [''], ['   '], ['invalid-address']];
    }

    #[DataProvider('missingContacts')]
    public function test_deleted_missing_or_unrelated_contact_is_not_used(string $change): void
    {
        match ($change) {
            'deleted contact' => DB::table('ticket_client_contacts')->where('id', 9)->update(['deleted_at' => now()]),
            'missing contact' => DB::table('ticket_client_contacts')->delete(),
            'unrelated contact' => DB::table('ticket_client_contacts')->where('id', 9)->update(['ticket_client_id' => 99]),
            'missing customer' => DB::table('ticket_clients')->delete(),
            'deleted customer' => DB::table('ticket_clients')->where('id', 8)->update(['deleted_at' => now()]),
            'no customer link' => DB::table('tickets')->where('id', 42)->update(['ticket_client_id' => null]),
        };
        $this->resend()->assertUnprocessable()->assertJsonPath('code', 'ticket_email_invalid');
        Mail::assertNothingSent();
    }

    public static function missingContacts(): array
    {
        return array_map(fn ($value) => [$value], [
            'deleted contact', 'missing contact', 'unrelated contact', 'missing customer', 'deleted customer', 'no customer link',
        ]);
    }

    public function test_guests_and_invalid_bearer_tokens_cannot_resend(): void
    {
        Auth::forgetGuards();
        $this->resend()->assertUnauthorized();
        $this->withToken('invalid-token')->resend()->assertUnauthorized();
        Mail::assertNothingSent();
    }

    public function test_read_permissions_do_not_allow_resending(): void
    {
        $this->operator->syncPermissions(['allTicket', 'editTicket']);
        $this->resend()->assertForbidden();
        Mail::assertNothingSent();
    }

    public function test_real_bearer_token_with_update_permission_can_resend(): void
    {
        $token = Auth::guard('api')->login($this->operator);
        Auth::forgetGuards();
        $this->withToken($token)->resend()->assertOk();
        Mail::assertSent(OuterTicketCreated::class, 1);
    }

    public function test_double_click_is_blocked_across_users_and_later_resend_reads_fresh_email(): void
    {
        $this->resend()->assertOk();
        DB::table('users')->insert(['id' => 2, 'username' => 'second-operator']);
        $other = User::findOrFail(2);
        $other->givePermissionTo('updateTicket');
        $this->actingAs($other, 'api');
        $this->resend()->assertStatus(429)->assertJsonPath('code', 'ticket_email_throttled')
            ->assertHeader('Retry-After');
        Mail::assertSentCount(1);

        $this->travel(61)->seconds();
        DB::table('ticket_client_contacts')->where('id', 9)->update(['email' => 'latest@example.com']);
        $this->resend()->assertOk()->assertExactJson(['message' => 'Email reinviata correttamente.']);
        Mail::assertSentCount(2);
        Mail::assertSent(OuterTicketCreated::class, fn ($mail) => $mail->hasTo('latest@example.com'));
    }

    public function test_an_in_flight_resend_is_blocked_without_sending(): void
    {
        $lock = Cache::lock('ticket-acceptance-resend:42:lock', 180);
        $this->assertTrue($lock->get());
        try {
            $this->resend()->assertStatus(429)->assertHeader('Retry-After');
            Mail::assertNothingSent();
        } finally {
            $lock->release();
        }
        $this->resend()->assertOk();
    }

    public function test_provider_failure_is_sanitized_logged_and_does_not_change_records_or_retry(): void
    {
        $before = $this->snapshot();
        Mail::shouldReceive('to')->once()->with('current@example.com')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new TransportException('Private provider detail with secret'));
        $this->resend()->assertStatus(502)->assertExactJson([
            'message' => "Impossibile confermare l'invio dell'email. Verificare la posta inviata prima di riprovare.",
            'code' => 'ticket_email_send_failed',
        ]);
        $this->resend()->assertStatus(429);
        $this->assertSame($before, $this->snapshot());
        Log::shouldHaveReceived('warning')->with('Ticket acceptance email resend failed.', \Mockery::on(
            fn ($context) => $context['ticket_id'] === 42 && $context['user_id'] === 1
                && ! str_contains(json_encode($context), 'secret')
        ))->once();
        Log::shouldNotHaveReceived('info', ['Ticket acceptance email resend accepted.', \Mockery::any()]);
    }
}
