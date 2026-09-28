<?php

namespace Tests\Feature;

use App\Services\Upload\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UploadServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['jwt.secret' => str_repeat('test-key', 8)]);
        Storage::fake('uploads');
        Storage::fake('public');
    }

    public static function attachmentNames(): array
    {
        return [
            ['signed.p7m'],
            ['invoice.pdf.p7m'],
            ['signed-upper.P7M'],
            ['message.eml'],
            ['message-upper.EML'],
            ['outlook.msg'],
            ['outlook-upper.MSG'],
        ];
    }

    #[DataProvider('attachmentNames')]
    public function test_single_upload_preserves_extension_and_bytes_when_mime_is_generic(string $name): void
    {
        $bytes = "Opaque attachment\x00\xff\r\n";
        $file = UploadedFile::fake()->createWithContent($name, $bytes)->mimeType('application/octet-stream');

        $service = app(UploadService::class);
        $path = $service->uploadFile(['file' => $file, 'uploadPath' => 'outertickets/42']);

        $this->assertStringStartsWith('uploads/outertickets/42/', $path);
        $this->assertStringEndsWith('___'.$name, $path);
        $diskPath = substr($path, strlen('uploads/'));
        Storage::disk('uploads')->assertExists($diskPath);
        $this->assertSame($bytes, Storage::disk('uploads')->get($diskPath));
        $this->assertSame([['path' => $path, 'actionStatus' => '']], $service->readFiles('outertickets-42'));

        $this->assertSame(1, $service->copyFiles('outertickets/42', 'tickets/42'));
        $this->assertSame($bytes, Storage::disk('uploads')->get('tickets/42/'.basename($path)));
    }

    public function test_multiple_upload_route_preserves_mixed_attachment_names_and_contents(): void
    {
        $files = [];
        $expected = [];
        foreach (self::attachmentNames() as [$name]) {
            $expected[$name] = 'Content for '.$name."\x00\xff";
            $files[] = UploadedFile::fake()->createWithContent($name, $expected[$name])->mimeType('application/octet-stream');
        }

        $response = $this->post('/api/v1/uploads/uploadmultiplefiles', [
            'uploadPath' => 'tickets/42',
            'files' => $files,
        ], ['Accept' => 'application/json'])->assertOk();

        $paths = $response->json('paths');
        $this->assertCount(count($files), $paths);
        foreach ($paths as $index => $path) {
            $name = $files[$index]->getClientOriginalName();
            $this->assertStringStartsWith('tickets/42/', $path);
            $this->assertStringEndsWith('___'.$name, $path);
            Storage::disk('public')->assertExists($path);
            $this->assertSame($expected[$name], Storage::disk('public')->get($path));
        }
    }

    public function test_update_files_route_stores_all_three_formats(): void
    {
        $files = [];
        foreach (['signed.p7m', 'message.eml', 'outlook.msg'] as $name) {
            $files[] = [
                'actionStatus' => 'create',
                'path' => UploadedFile::fake()->createWithContent($name, 'content:'.$name)->mimeType('application/octet-stream'),
            ];
        }

        $this->put('/api/v1/uploads/updatefiles', [
            'directory' => 'tickets-42',
            'files' => $files,
        ], ['Accept' => 'application/json'])->assertOk();

        $paths = Storage::disk('uploads')->files('tickets/42');
        $this->assertCount(3, $paths);
        foreach ($paths as $path) {
            $name = explode('___', basename($path), 2)[1];
            $this->assertContains($name, ['signed.p7m', 'message.eml', 'outlook.msg']);
            $this->assertSame('content:'.$name, Storage::disk('uploads')->get($path));
        }
    }

    public function test_eml_upload_with_real_mime_detection_preserves_message_bytes(): void
    {
        $bytes = "From: sender@example.com\r\nTo: recipient@example.com\r\nSubject: Test\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=utf-8\r\n\r\nMessage body\r\n";
        $source = UploadedFile::fake()->createWithContent('original.eml', $bytes);
        $file = new UploadedFile($source->getRealPath(), 'original.eml', null, UPLOAD_ERR_OK, true);

        $path = app(UploadService::class)->uploadFile(['file' => $file, 'uploadPath' => 'tickets/42']);

        $this->assertStringEndsWith('___original.eml', $path);
        $this->assertSame($bytes, Storage::disk('uploads')->get(substr($path, strlen('uploads/'))));
    }

    public function test_other_extensions_keep_existing_mime_based_naming(): void
    {
        $file = UploadedFile::fake()->createWithContent('original.wrong', '%PDF-1.4')->mimeType('application/pdf');
        $service = app(UploadService::class);
        $singlePath = $service->uploadFile(['file' => $file, 'uploadPath' => 'tickets/42']);
        $multiplePaths = $service->uploadMultipleFile(['files' => [$file], 'uploadPath' => 'tickets/42']);

        $this->assertStringEndsWith('___original.pdf', $singlePath);
        $this->assertStringEndsWith('___original.pdf', $multiplePaths[0]);
        $this->assertSame('%PDF-1.4', Storage::disk('uploads')->get(substr($singlePath, strlen('uploads/'))));
        $this->assertSame('%PDF-1.4', Storage::disk('public')->get($multiplePaths[0]));
    }
}
