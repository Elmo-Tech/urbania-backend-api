<?php

namespace Tests\Feature;

use App\Mail\ClientTicketEmail;
use App\Mail\OuterTicketRejected;
use App\Models\ClientOuterTicket;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class MicrosoftGraphMailTest extends TestCase
{
    private const TOKEN_URL = 'https://login.microsoftonline.com/test-tenant/oauth2/v2.0/token';

    private const SEND_URL = 'https://graph.microsoft.com/v1.0/users/tributi%40urbaniaweb.it/sendMail';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'jwt.secret' => str_repeat('test-key', 8),
            'mail.default' => 'microsoft_graph',
            'mail.from' => ['address' => 'tributi@urbaniaweb.it', 'name' => 'Tributi Urbania'],
            'services.microsoft_graph_mail' => [
                'tenant_id' => 'test-tenant',
                'client_id' => 'test-client',
                'client_secret' => 'test-secret',
            ],
        ]);
        Cache::flush();
        Http::preventStrayRequests();
        Mail::purge('microsoft_graph');
    }

    private function fakeSuccess(): void
    {
        Http::fake([
            self::TOKEN_URL => Http::response(['access_token' => 'test-token', 'expires_in' => 3600]),
            self::SEND_URL => Http::response('', 202),
        ]);
    }

    private function sendText(): void
    {
        Mail::raw('Test body', fn (Message $message) => $message->to('recipient@example.com')->subject('Test subject'));
    }

    public function test_rejection_email_sends_saved_reason_via_graph_with_html_escaped(): void
    {
        $this->fakeSuccess();
        $ticket = (new ClientOuterTicket)->forceFill([
            'number' => 'T-000042',
            'rejection_reason' => "Missing document\n<script>alert(1)</script>",
        ]);
        Mail::to('recipient@example.com')->send(new OuterTicketRejected($ticket));

        Http::assertSent(fn (Request $request) => $request->url() === self::SEND_URL
            && $request['message']['subject'] === 'Urbania ha rifiutato la segnalazione'
            && str_contains($request['message']['body']['content'], 'T-000042')
            && str_contains($request['message']['body']['content'], 'Missing document')
            && str_contains($request['message']['body']['content'], '&lt;script&gt;')
            && ! str_contains($request['message']['body']['content'], '<script>'));
    }

    public function test_sends_existing_mailable_with_html_binary_attachment_and_sender(): void
    {
        $this->fakeSuccess();
        $bytes = "%PDF-1.4\n\x00\xff\n";
        $file = UploadedFile::fake()->createWithContent('document.pdf', $bytes);
        Mail::to('recipient@example.com')->send(new ClientTicketEmail('Requested subject', '<p>Ticket reply</p>', [$file->getRealPath()]));

        Http::assertSent(fn (Request $request) => $request->url() === self::TOKEN_URL
            && $request['grant_type'] === 'client_credentials'
            && $request['scope'] === 'https://graph.microsoft.com/.default'
            && $request['client_id'] === 'test-client'
            && $request['client_secret'] === 'test-secret');
        Http::assertSent(fn (Request $request) => $request->url() === self::SEND_URL
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request['message']['subject'] === 'Requested subject'
            && $request['message']['from']['emailAddress'] === ['address' => 'tributi@urbaniaweb.it', 'name' => 'Tributi Urbania']
            && $request['message']['toRecipients'][0]['emailAddress']['address'] === 'recipient@example.com'
            && $request['message']['body']['contentType'] === 'HTML'
            && str_contains($request['message']['body']['content'], 'Ticket reply')
            && base64_decode($request['message']['attachments'][0]['contentBytes']) === $bytes
            && $request['saveToSentItems'] === true);
    }

    public function test_preserves_cc_bcc_reply_to_and_inline_images(): void
    {
        $this->fakeSuccess();
        Mail::html('<img src="cid:logo.png">', function (Message $message) {
            $message->to('recipient@example.com')->cc('cc@example.com')->bcc('bcc@example.com')
                ->replyTo('reply@example.com')->subject('Inline');
            $message->getSymfonyMessage()->embed('image-bytes', 'logo.png', 'image/png');
        });

        Http::assertSent(function (Request $request) {
            if ($request->url() !== self::SEND_URL) {
                return false;
            }
            $message = $request['message'];
            $image = $message['attachments'][0];

            return $message['ccRecipients'][0]['emailAddress']['address'] === 'cc@example.com'
                && $message['bccRecipients'][0]['emailAddress']['address'] === 'bcc@example.com'
                && $message['replyTo'][0]['emailAddress']['address'] === 'reply@example.com'
                && $image['isInline'] === true
                && str_contains($message['body']['content'], 'cid:'.$image['contentId'])
                && base64_decode($image['contentBytes']) === 'image-bytes';
        });
    }

    public function test_reuses_token_then_refreshes_before_expiry(): void
    {
        $this->fakeSuccess();
        $this->sendText();
        $this->sendText();
        Http::assertSentCount(3);
        $this->travel(3541)->seconds();
        $this->sendText();
        Http::assertSentCount(5);
        $this->travelBack();
    }

    public function test_retries_once_after_rejected_token(): void
    {
        Http::fake([
            self::TOKEN_URL => Http::sequence()
                ->push(['access_token' => 'old-token', 'expires_in' => 3600])
                ->push(['access_token' => 'new-token', 'expires_in' => 3600]),
            self::SEND_URL => Http::sequence()->push('', 401)->push('', 202),
        ]);
        $this->sendText();
        Http::assertSentCount(4);
        Http::assertSent(fn (Request $request) => $request->url() === self::SEND_URL
            && $request->hasHeader('Authorization', 'Bearer new-token')
            && $request['message']['body'] === ['contentType' => 'Text', 'content' => 'Test body']);
    }

    public function test_token_failure_does_not_leak_response_or_send_mail(): void
    {
        Http::fake([self::TOKEN_URL => Http::response(['error_description' => 'private test-secret'], 400)]);
        try {
            $this->sendText();
            $this->fail('Expected token failure.');
        } catch (TransportException $e) {
            $this->assertSame('Microsoft Graph token request failed (HTTP 400).', $e->getMessage());
            $this->assertNull($e->getPrevious());
        }
        Http::assertSentCount(1);
    }

    public function test_malformed_token_response_is_rejected(): void
    {
        Http::fake([self::TOKEN_URL => Http::response(['access_token' => '', 'expires_in' => 3600])]);
        $this->expectExceptionMessage('Microsoft Graph returned an invalid token response.');
        $this->sendText();
    }

    public function test_missing_credentials_fail_before_http_request(): void
    {
        config(['services.microsoft_graph_mail.client_secret' => '']);
        Http::fake();
        try {
            $this->sendText();
            $this->fail('Expected configuration failure.');
        } catch (TransportException $e) {
            $this->assertStringContainsString('missing client_secret', $e->getMessage());
        }
        Http::assertNothingSent();
    }

    public static function sendFailures(): array
    {
        return [[403], [429], [500], [200]];
    }

    #[DataProvider('sendFailures')]
    public function test_permission_throttling_and_server_errors_are_not_retried_or_reported_as_success(int $status): void
    {
        Http::fake([
            self::TOKEN_URL => Http::response(['access_token' => 'test-token', 'expires_in' => 3600]),
            self::SEND_URL => Http::response('private-response', $status),
        ]);
        try {
            $this->sendText();
            $this->fail('Expected sending failure.');
        } catch (TransportException $e) {
            $this->assertSame('Microsoft Graph rejected the email (HTTP '.$status.').', $e->getMessage());
        }
        $this->assertCount(1, Http::recorded(fn (Request $request) => $request->url() === self::SEND_URL));
    }

    public function test_connection_failure_is_sanitized_and_not_retried(): void
    {
        $attempts = 0;
        Http::fake(function (Request $request) use (&$attempts) {
            if ($request->url() === self::TOKEN_URL) {
                return Http::response(['access_token' => 'test-token', 'expires_in' => 3600]);
            }
            $attempts++;
            throw new ConnectionException('private token');
        });
        try {
            $this->sendText();
            $this->fail('Expected connection failure.');
        } catch (TransportException $e) {
            $this->assertStringContainsString('Delivery is unconfirmed', $e->getMessage());
            $this->assertStringNotContainsString('private token', $e->getMessage());
            $this->assertNull($e->getPrevious());
        }
        $this->assertSame(1, $attempts);
    }

    public function test_large_attachment_is_rejected_before_request(): void
    {
        Http::fake();
        try {
            Mail::raw('Attachment', function (Message $message) {
                $message->to('recipient@example.com')->subject('Large')
                    ->attachData(str_repeat('a', 3 * 1024 * 1024), 'large.txt');
            });
            $this->fail('Expected size failure.');
        } catch (TransportException $e) {
            $this->assertStringContainsString('smaller than 3 MB', $e->getMessage());
        }
        Http::assertNothingSent();
    }

    public function test_combined_attachments_are_checked_after_base64_encoding(): void
    {
        Http::fake();
        try {
            Mail::raw('Attachments', function (Message $message) {
                $message->to('recipient@example.com')->subject('Large')
                    ->attachData(str_repeat('a', 2 * 1024 * 1024), 'first.txt')
                    ->attachData(str_repeat('b', 2 * 1024 * 1024), 'second.txt');
            });
            $this->fail('Expected combined size failure.');
        } catch (TransportException $e) {
            $this->assertStringContainsString('4 MB request limit', $e->getMessage());
        }
        Http::assertNothingSent();
    }

    public function test_general_email_route_preserves_input_fields_and_attachment_names(): void
    {
        $this->fakeSuccess();
        $this->post('/api/v1/email/send', [
            'emailRecipient' => 'recipient@example.com',
            'emailSubject' => 'General message',
            'emailBody' => '<p>Original HTML</p>',
            'emailAttachments' => [UploadedFile::fake()->createWithContent('report.pdf', 'file-bytes')],
        ], ['Accept' => 'application/json'])->assertOk()->assertJson(['message' => 'message has been sent !']);

        Http::assertSent(fn (Request $request) => $request->url() === self::SEND_URL
            && $request['message']['body']['content'] === '<p>Original HTML</p>'
            && $request['message']['attachments'][0]['name'] === 'report.pdf'
            && base64_decode($request['message']['attachments'][0]['contentBytes']) === 'file-bytes');
    }

    public static function clientAttachments(): array
    {
        return [
            ['signed.pdf.p7m', 'application/pkcs7-mime'],
            ['signed.p7m', 'application/x-pkcs7-mime'],
            ['signed.p7m', 'application/pkcs7-signature'],
            ['signed.P7M', 'application/octet-stream'],
            ['message.eml', 'message/rfc822'],
            ['message.eml', 'text/plain'],
            ['message.EML', 'application/octet-stream'],
            ['message.msg', 'application/vnd.ms-outlook'],
            ['message.msg', 'application/CDFV2'],
            ['message.msg', 'application/x-ole-storage'],
            ['message.msg', 'application/x-cdf'],
            ['message.MSG', 'application/octet-stream'],
            ['existing.pdf', 'application/pdf'],
        ];
    }

    #[DataProvider('clientAttachments')]
    public function test_client_email_route_sends_supported_attachments_unchanged(string $name, string $mime): void
    {
        $this->fakeSuccess();
        $this->actingAs((new User)->forceFill(['id' => 1]), 'api');
        Storage::fake('uploads');
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();
        $bytes = "Attachment payload\x00\xff\n";
        $file = UploadedFile::fake()->createWithContent($name, $bytes)->mimeType($mime);

        $this->post('/api/v1/client-email/send', [
            'email' => 'recipient@example.com',
            'subject' => 'Client attachment',
            'content' => 'Attached document',
            'attachments' => [$file],
        ], ['Accept' => 'application/json'])->assertOk()->assertJson(['message' => 'Email Sent!']);

        Http::assertSent(fn (Request $request) => $request->url() === self::SEND_URL
            && $request['message']['attachments'][0]['name'] === $name
            && base64_decode($request['message']['attachments'][0]['contentBytes']) === $bytes);
        $this->assertSame([], Storage::disk('uploads')->allFiles());
    }

    public static function rejectedClientAttachments(): array
    {
        return [
            ['unknown.bin', 'application/octet-stream'],
            ['archive.zip', 'application/zip'],
            ['script.php', 'application/pdf'],
            ['script.p7m', 'application/x-php'],
            ['script.eml', 'text/html'],
            ['program.msg', 'application/x-dosexec'],
        ];
    }

    #[DataProvider('rejectedClientAttachments')]
    public function test_client_email_route_keeps_rejecting_unsupported_attachments(string $name, string $mime): void
    {
        Http::fake();
        $this->actingAs((new User)->forceFill(['id' => 1]), 'api');
        DB::shouldReceive('beginTransaction')->never();
        $this->post('/api/v1/client-email/send', [
            'email' => 'recipient@example.com',
            'subject' => 'Unsupported attachment',
            'content' => 'Test',
            'attachments' => [UploadedFile::fake()->create($name, 1, $mime)],
        ], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('attachments.0');

        Http::assertNothingSent();
    }

    public function test_general_route_reports_graph_failure_as_json(): void
    {
        Http::fake([
            self::TOKEN_URL => Http::response(['access_token' => 'test-token', 'expires_in' => 3600]),
            self::SEND_URL => Http::response('private-response', 403),
        ]);
        $this->postJson('/api/v1/email/send', [
            'emailRecipient' => 'recipient@example.com', 'emailSubject' => 'Test', 'emailBody' => 'Test',
        ])->assertStatus(502)->assertJson(['error' => 'Microsoft Graph rejected the email (HTTP 403).']);
    }
}
