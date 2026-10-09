<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use App\Services\Ticket\TicketClient\TicketClientService;
use App\Services\Upload\UploadService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SollecitoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sollecito_test',
            'database.connections.sollecito_test' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => false,
            ],
            'jwt.secret' => str_repeat('test-key', 8),
        ]);
        Mail::fake();
        Storage::fake('uploads');
        $this->travelTo(now()->setDate(2026, 9, 28)->setTime(12, 30));

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('status');
            foreach (['description', 'email_token', 'ticket_number', 'segnalazione', 'urgenza',
                'notify_date', 'end_date', 'worker_id', 'connect_type_id', 'client_id', 'ticket_client_id',
                'contract_id', 'contract_id_2', 'service_id', 'esito', 'note', 'status_date', 'anno',
                'tipologia_istanza'] as $column) {
                $table->text($column)->nullable();
            }
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('parameter_values', function (Blueprint $table) {
            $table->id();
            $table->integer('parameter_id');
            $table->string('parameter_value');
            $table->text('description')->nullable();
            $table->softDeletes();
        });
        DB::table('parameter_values')->insert([
            ['id' => 91, 'parameter_id' => 17, 'parameter_value' => 'Non urgente', 'description' => '0'],
            ['id' => 92, 'parameter_id' => 17, 'parameter_value' => 'Sollecitato', 'description' => '7'],
        ]);
        DB::table('tickets')->insert([
            'id' => 3235, 'status' => 1, 'description' => 'Original description',
            'email_token' => 'customer-test-token', 'ticket_number' => '3235_2026',
            'segnalazione' => '5', 'urgenza' => '0', 'updated_by' => 17,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function attachment(string $name = 'document.pdf', string $mime = 'application/pdf'): array
    {
        return [
            'name' => $name,
            'path' => UploadedFile::fake()->createWithContent($name, 'Attachment '.$name)->mimeType($mime),
            'actionStatus' => 'create',
        ];
    }

    private function send(array $overrides = [])
    {
        // Match the browser's multipart POST with method spoofing and nested files.
        return $this->post('/api/v1/client-outer-tickets/update', array_replace([
            '_method' => 'PUT', 'ticketId' => '3235', 'token' => 'customer-test-token',
            'sollecito' => '1', 'message' => 'test message', 'uploadPath' => 'tickets/3235',
        ], $overrides), ['Accept' => 'application/json']);
    }

    public function test_public_sollecito_changes_urgency_preserves_status_and_saves_message_and_excel(): void
    {
        $file = $this->attachment('ElmoTech_A.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->send(['files' => [$file]])->assertOk();

        $ticket = Ticket::findOrFail(3235);
        $description = 'Original description'.PHP_EOL.'28/09/2026 12:30'.PHP_EOL.'test message';
        $this->assertEquals(1, $ticket->status);
        $this->assertSame($description, $ticket->description);
        $this->assertSame('5', $ticket->segnalazione);
        $this->assertSame('7', $ticket->urgenza);
        $this->assertNull($ticket->updated_by);

        $files = $this->getJson('/api/v1/uploads/getfiles?directory=tickets-3235')->assertOk()->json();
        $this->assertCount(1, $files);
        $path = $files[0]['path'];
        $this->assertStringStartsWith('uploads/tickets/3235/', $path);
        $this->assertStringEndsWith('___ElmoTech_A.xlsx', $path);
        $this->assertSame('Attachment ElmoTech_A.xlsx', Storage::disk('uploads')->get(substr($path, 8)));

        $this->actingAs((new User)->forceFill(['id' => 1]), 'api');
        $this->withoutMiddleware(\App\Http\Middleware\JWTAuthentication::class);
        $this->getJson('/api/v1/tickets/edit?ticketId=3235')->assertOk()
            ->assertJsonPath('status', 1)->assertJsonPath('description', $description)
            ->assertJsonPath('urgenza', 92);
        Mail::assertNothingSent();
    }

    public function test_repeated_customer_request_appends_once_per_request_not_once_per_message(): void
    {
        $entry = PHP_EOL.'28/09/2026 12:30'.PHP_EOL.'test message';

        $this->send()->assertOk();
        $this->assertSame('Original description'.$entry, Ticket::findOrFail(3235)->description);

        $this->send()->assertOk();
        $this->assertSame('Original description'.$entry.$entry, Ticket::findOrFail(3235)->description);
    }

    public function test_customer_service_update_does_not_duplicate_sollecito_text_or_date(): void
    {
        $this->send()->assertOk();
        $description = Ticket::findOrFail(3235)->description;
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('closer_id')->nullable();
        });
        // Isolate contact maintenance, while running the real ticket update and persistence.
        $this->mock(TicketClientService::class, function ($mock) {
            $mock->shouldReceive('updateTicketClient')->twice()->andReturn(3198);
        });
        $this->actingAs((new User)->forceFill(['id' => 1]), 'api');
        $this->withoutMiddleware(\App\Http\Middleware\JWTAuthentication::class);
        $payload = [
            'ticketId' => 3235, 'ticketClientId' => 3198, 'clientId' => 10,
            'contractId' => '18##16', 'serviceId' => 27, 'workerId' => '',
            'notifyDate' => null, 'status' => 1, 'description' => $description,
            'connectTypeId' => null, 'note' => null, 'anno' => ['2020'], 'urgenza' => 92,
        ];

        foreach ([1, 2] as $attempt) {
            $this->putJson('/api/v1/tickets/update', $payload)->assertOk();
            $this->assertSame($description, Ticket::findOrFail(3235)->description);
            $this->getJson('/api/v1/tickets/edit?ticketId=3235')->assertOk()
                ->assertJsonPath('description', $description);
            $this->assertSame(1, substr_count(Ticket::findOrFail(3235)->description, '28/09/2026 12:30'));
            $this->assertSame(1, substr_count(Ticket::findOrFail(3235)->description, 'test message'));
        }
    }

    public function test_signed_and_email_attachments_are_saved_under_verified_ticket_not_supplied_path(): void
    {
        $files = [];
        foreach (['invoice.pdf.p7m', 'message.eml', 'outlook.MSG'] as $name) {
            $files[] = $this->attachment($name, 'application/octet-stream');
        }
        $this->send(['files' => $files, 'uploadPath' => '../../tickets/999'])->assertOk();
        $paths = Storage::disk('uploads')->allFiles();
        $this->assertCount(3, $paths);
        foreach ($paths as $path) {
            $this->assertStringStartsWith('tickets/3235/', $path);
            $name = explode('___', basename($path), 2)[1];
            $this->assertContains($name, ['invoice.pdf.p7m', 'message.eml', 'outlook.MSG']);
            $this->assertSame('Attachment '.$name, Storage::disk('uploads')->get($path));
        }
    }

    public function test_same_named_attachments_do_not_overwrite_each_other_or_old_files(): void
    {
        Storage::disk('uploads')->put('tickets/3235/existing.pdf', 'old');
        $this->send(['files' => [$this->attachment(), $this->attachment()]])->assertOk();
        $this->send(['files' => [$this->attachment()]])->assertOk();
        $this->assertCount(4, Storage::disk('uploads')->files('tickets/3235'));
        $this->assertSame('old', Storage::disk('uploads')->get('tickets/3235/existing.pdf'));
        $this->assertSame(2, substr_count(Ticket::find(3235)->description, 'test message'));
    }

    public function test_no_message_preserves_description_and_no_files_are_required(): void
    {
        $this->send(['message' => '   '])->assertOk();
        $this->assertSame('Original description', Ticket::find(3235)->description);
        $this->assertEquals(1, Ticket::find(3235)->status);
        $this->assertSame([], Storage::disk('uploads')->allFiles());
    }

    public function test_first_message_has_no_leading_separator(): void
    {
        DB::table('tickets')->where('id', 3235)->update(['description' => null]);
        $this->send()->assertOk();
        $this->assertSame('28/09/2026 12:30'.PHP_EOL.'test message', Ticket::find(3235)->description);
    }

    public function test_zero_restores_non_urgent_without_changing_status(): void
    {
        DB::table('tickets')->where('id', 3235)->update(['status' => 3, 'urgenza' => '7']);
        $this->send(['sollecito' => '0', 'message' => ''])->assertOk()->assertJsonPath('status', 3);
        $this->assertEquals(3, Ticket::find(3235)->status);
        $this->assertSame('0', Ticket::find(3235)->urgenza);
    }

    public function test_repeated_zero_and_one_requests_can_toggle_urgency_both_ways(): void
    {
        DB::table('tickets')->where('id', 3235)->update(['status' => 3]);
        $this->withoutMiddleware(\App\Http\Middleware\JWTAuthentication::class);
        $values = [1, 1, 0, 0, '1', '0', true, false, 1];
        foreach ($values as $index => $value) {
            $this->send(['sollecito' => $value, 'message' => 'Follow-up '.$index])->assertOk();
            $ticket = Ticket::find(3235);
            $this->assertEquals(1, $ticket->status);
            $this->assertSame($value ? '7' : '0', $ticket->urgenza);
            $this->assertStringContainsString('Follow-up '.$index, $ticket->description);
            $this->getJson('/api/v1/tickets/edit?ticketId=3235')->assertOk()
                ->assertJsonPath('status', 1)->assertJsonPath('urgenza', $value ? 92 : 91);
        }
        $this->assertSame(count($values), substr_count(Ticket::find(3235)->description, 'Follow-up'));
        Mail::assertNothingSent();
    }

    public function test_zero_falls_back_to_non_urgent_name_when_91_does_not_match(): void
    {
        DB::table('parameter_values')->where('id', 91)->update(['parameter_value' => 'Urgente']);
        DB::table('parameter_values')->insert([
            'id' => 191, 'parameter_id' => 17, 'parameter_value' => ' Non urgente ', 'description' => '8',
        ]);
        $this->send(['sollecito' => 0])->assertOk();
        $this->assertSame('8', Ticket::find(3235)->urgenza);
        $this->assertEquals(1, Ticket::find(3235)->status);
        $this->withoutMiddleware(\App\Http\Middleware\JWTAuthentication::class);
        $this->getJson('/api/v1/tickets/edit?ticketId=3235')->assertOk()->assertJsonPath('urgenza', 191);
    }

    public function test_missing_non_urgent_option_rejects_zero_without_partial_changes(): void
    {
        DB::table('parameter_values')->where('id', 91)->delete();
        $this->send(['sollecito' => 0, 'files' => [$this->attachment()]])->assertUnprocessable();
        $this->assertUnchanged();
    }

    public static function suspendedFollowUps(): array
    {
        return [
            'reminder only' => [['sollecito' => '1'], false, '7'],
            'message only' => [['message' => 'More information'], false, '0'],
            'message with reminder disabled' => [['sollecito' => '0', 'message' => 'More information'], false, '0'],
            'attachment only' => [[], true, '0'],
            'attachment with reminder disabled' => [['sollecito' => '0'], true, '0'],
            'reminder and integration' => [['sollecito' => '1', 'message' => 'More information'], true, '7'],
        ];
    }

    #[DataProvider('suspendedFollowUps')]
    public function test_customer_follow_up_reactivates_suspended_ticket(array $payload, bool $withFile, string $urgency): void
    {
        DB::table('tickets')->where('id', 3235)->update([
            'status' => 3, 'status_date' => '2026-09-01 00:00:00',
        ]);
        if ($withFile) {
            $payload['files'] = [$this->attachment()];
        }
        $this->post('/api/v1/client-outer-tickets/update', array_merge([
            '_method' => 'PUT', 'ticketId' => '3235', 'token' => 'customer-test-token',
        ], $payload), ['Accept' => 'application/json'])->assertOk()->assertJsonPath('status', 1);

        $ticket = Ticket::findOrFail(3235);
        $this->assertEquals(1, $ticket->status);
        $this->assertSame('2026-09-28 12:30:00', $ticket->status_date);
        $this->assertNull($ticket->end_date);
        $this->assertSame($urgency, $ticket->urgenza);
        $this->assertSame('5', $ticket->segnalazione);
        $this->assertSame(isset($payload['message'])
            ? 'Original description'.PHP_EOL.'28/09/2026 12:30'.PHP_EOL.$payload['message']
            : 'Original description', $ticket->description);
        $this->assertCount($withFile ? 1 : 0, Storage::disk('uploads')->allFiles());
        $this->withoutMiddleware(\App\Http\Middleware\JWTAuthentication::class);
        $this->getJson('/api/v1/tickets/edit?ticketId=3235')->assertOk()->assertJsonPath('status', 1);
        Mail::assertNothingSent();
    }

    public function test_missing_sollecito_flag_preserves_urgency_when_reactivating(): void
    {
        DB::table('tickets')->where('id', 3235)->update(['status' => 3, 'urgenza' => '7']);
        $this->putJson('/api/v1/client-outer-tickets/update', [
            'ticketId' => 3235, 'token' => 'customer-test-token', 'message' => 'Follow-up',
        ])->assertOk()->assertJsonPath('status', 1);
        $this->assertEquals(1, Ticket::find(3235)->status);
        $this->assertSame('7', Ticket::find(3235)->urgenza);
    }

    public function test_empty_update_does_not_reactivate_a_suspended_ticket(): void
    {
        DB::table('tickets')->where('id', 3235)->update([
            'status' => 3, 'status_date' => '2026-09-01 00:00:00',
        ]);
        foreach ([[], ['message' => '   ', 'files' => []], ['sollecito' => '0']] as $payload) {
            $this->putJson('/api/v1/client-outer-tickets/update', array_merge([
                'ticketId' => 3235, 'token' => 'customer-test-token',
            ], $payload))->assertOk()->assertJsonPath('status', 3);
            $this->assertUnchanged(3);
            $this->assertSame('2026-09-01 00:00:00', Ticket::find(3235)->status_date);
        }
    }

    public function test_follow_up_does_not_reset_the_status_date_of_an_active_ticket(): void
    {
        DB::table('tickets')->where('id', 3235)->update(['status_date' => '2026-09-01 00:00:00']);
        $this->send()->assertOk()->assertJsonPath('status', 1);
        $this->assertSame('2026-09-01 00:00:00', Ticket::find(3235)->status_date);
    }

    public function test_invalid_follow_up_does_not_reactivate_a_suspended_ticket(): void
    {
        DB::table('tickets')->where('id', 3235)->update(['status' => 3]);
        $this->send(['token' => 'wrong'])->assertUnprocessable();
        $this->assertUnchanged(3);
        DB::table('parameter_values')->where('id', 92)->delete();
        $this->send(['files' => [$this->attachment()]])->assertUnprocessable();
        $this->assertUnchanged(3);
    }

    public static function fallbackOptions(): array
    {
        return [
            [['parameter_value' => 'Non urgente']],
            [['parameter_id' => 99]],
            [['deleted_at' => '2026-09-01 00:00:00']],
        ];
    }

    #[DataProvider('fallbackOptions')]
    public function test_urgency_lookup_falls_back_by_name_when_92_is_not_the_active_urgency_option(array $changes): void
    {
        DB::table('parameter_values')->where('id', 92)->update($changes);
        DB::table('parameter_values')->insert([
            'id' => 192, 'parameter_id' => 17, 'parameter_value' => ' Sollecitato ', 'description' => '9',
        ]);
        $this->send()->assertOk();
        $this->assertSame('9', Ticket::find(3235)->urgenza);
        $this->assertEquals(1, Ticket::find(3235)->status);
        $this->withoutMiddleware(\App\Http\Middleware\JWTAuthentication::class);
        $this->getJson('/api/v1/tickets/edit?ticketId=3235')->assertOk()->assertJsonPath('urgenza', 192);
    }

    public function test_valid_92_is_preferred_over_another_same_named_option(): void
    {
        DB::table('parameter_values')->insert([
            'id' => 192, 'parameter_id' => 17, 'parameter_value' => 'Sollecitato', 'description' => '9',
        ]);
        $this->send()->assertOk();
        $this->assertSame('7', Ticket::find(3235)->urgenza);
    }

    public function test_missing_or_ambiguous_urgency_prevents_partial_changes(): void
    {
        DB::table('parameter_values')->where('id', 92)->delete();
        $this->send(['files' => [$this->attachment()]])->assertUnprocessable();
        $this->assertUnchanged();
        DB::table('parameter_values')->insert([
            ['id' => 192, 'parameter_id' => 17, 'parameter_value' => 'Sollecitato', 'description' => '7'],
            ['id' => 193, 'parameter_id' => 17, 'parameter_value' => 'Sollecitato', 'description' => '9'],
        ]);
        $this->send()->assertUnprocessable();
        $this->assertUnchanged();
    }

    public function test_empty_or_conflicting_stored_urgency_value_is_rejected(): void
    {
        foreach ([null, '', '0'] as $value) {
            DB::table('parameter_values')->where('id', 92)->update(['description' => $value]);
            $this->send()->assertUnprocessable();
            $this->assertUnchanged();
        }
    }

    public static function invalidRequests(): array
    {
        return [
            [['token' => null]], [['token' => 'wrong']], [['ticketId' => 999]],
            [['sollecito' => 2]], [['message' => ['bad']]], [['files' => 'bad']],
            [['files' => [['actionStatus' => 'create', 'path' => 'not a file']]]],
        ];
    }

    #[DataProvider('invalidRequests')]
    public function test_invalid_requests_do_not_write_anything(array $overrides): void
    {
        $this->send(array_replace(['files' => [$this->attachment()]], $overrides))->assertUnprocessable();
        $this->assertUnchanged();
    }

    public function test_closed_ticket_rejects_request_without_writing_files(): void
    {
        DB::table('tickets')->where('id', 3235)->update(['status' => 2]);
        $this->send(['files' => [$this->attachment()]])->assertStatus(409);
        $this->assertUnchanged(2);
    }

    public function test_ticket_without_token_cannot_be_changed_by_omitting_token(): void
    {
        DB::table('tickets')->where('id', 3235)->update(['email_token' => null]);
        $this->send(['token' => null, 'files' => [$this->attachment()]])->assertUnprocessable();
        $this->assertUnchanged();
    }

    public function test_invalid_later_attachment_prevents_all_uploads(): void
    {
        $this->send(['files' => [
            $this->attachment(),
            ['actionStatus' => 'create', 'path' => UploadedFile::fake()->create('large.pdf', 20481, 'application/pdf')],
        ]])->assertUnprocessable()->assertJsonValidationErrors('files.1.path');
        $this->assertUnchanged();
    }

    public function test_delete_action_and_unsafe_file_are_rejected_before_changes(): void
    {
        $file = $this->attachment();
        $file['actionStatus'] = 'delete';
        $this->send(['files' => [$file]])->assertUnprocessable();
        $this->send(['files' => [$this->attachment('shell.php', 'application/x-httpd-php')]])->assertUnprocessable();
        $this->send(['files' => [$this->attachment('shell.pdf', 'application/x-httpd-php')]])->assertUnprocessable();
        $this->assertUnchanged();
    }

    public static function updatableStatuses(): array
    {
        return [[1], [3]];
    }

    #[DataProvider('updatableStatuses')]
    public function test_failure_on_second_upload_rolls_back_database_and_removes_only_new_files(int $status): void
    {
        DB::table('tickets')->where('id', 3235)->update([
            'status' => $status, 'status_date' => '2026-09-01 00:00:00',
        ]);
        Storage::disk('uploads')->put('tickets/3235/existing.pdf', 'old');
        $realService = new UploadService;
        $count = 0;
        $this->mock(UploadService::class)->shouldReceive('uploadFile')->twice()
            ->andReturnUsing(function ($data) use ($realService, &$count) {
                if (++$count === 2) {
                    throw new \RuntimeException('Storage unavailable');
                }

                return $realService->uploadFile($data);
            });
        $this->send(['files' => [$this->attachment(), $this->attachment()]])->assertStatus(500);
        $this->assertSame(['tickets/3235/existing.pdf'], Storage::disk('uploads')->allFiles());
        $this->assertSame('old', Storage::disk('uploads')->get('tickets/3235/existing.pdf'));
        $this->assertEquals($status, Ticket::find(3235)->status);
        $this->assertSame('2026-09-01 00:00:00', Ticket::find(3235)->status_date);
        $this->assertSame('Original description', Ticket::find(3235)->description);
        $this->assertEquals(17, Ticket::find(3235)->updated_by);
        $this->assertSame('0', Ticket::find(3235)->urgenza);
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_false_storage_result_is_not_reported_as_success(): void
    {
        Storage::shouldReceive('disk')->with('uploads')->once()->andReturnSelf();
        Storage::shouldReceive('putFileAs')->once()->andReturn(false);
        $this->send(['files' => [$this->attachment()]])->assertStatus(500);
        $this->assertEquals(1, Ticket::find(3235)->status);
        $this->assertSame('Original description', Ticket::find(3235)->description);
    }

    public function test_audit_trait_still_records_authenticated_updates(): void
    {
        $this->actingAs((new User)->forceFill(['id' => 12]), 'api');
        $this->send()->assertOk();
        $this->assertEquals(12, Ticket::find(3235)->updated_by);
    }

    private function assertUnchanged(int $status = 1): void
    {
        $this->assertEquals($status, Ticket::find(3235)->status);
        $this->assertSame('Original description', Ticket::find(3235)->description);
        $this->assertSame('0', Ticket::find(3235)->urgenza);
        $this->assertSame([], Storage::disk('uploads')->allFiles());
        $this->assertSame(0, DB::transactionLevel());
        Mail::assertNothingSent();
    }
}
