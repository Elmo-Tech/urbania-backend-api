<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ClientExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'client_export_test',
            'database.connections.client_export_test' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
            ],
            'jwt.secret' => str_repeat('test-key', 8),
        ]);

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            foreach (['company_name', 'name_acronym', 'people_number', 'trade_register', 'cf', 'secret_info'] as $column) {
                $table->string($column)->nullable();
            }
            $table->boolean('urbania_client')->default(false);
            $table->softDeletes();
        });

        DB::table('clients')->insert([
            ['id' => 1, 'company_name' => 'Alpha', 'name_acronym' => 'ألفا', 'people_number' => '1000', 'trade_register' => '00123456789', 'cf' => 'CF-ONE', 'secret_info' => 'never-export-this', 'deleted_at' => null],
            ['id' => 2, 'company_name' => 'Beta', 'name_acronym' => 'B', 'people_number' => '2000', 'trade_register' => '00987654321', 'cf' => 'CF-TWO', 'secret_info' => 'never-export-this', 'deleted_at' => null],
            ['id' => 3, 'company_name' => 'Gamma', 'name_acronym' => null, 'people_number' => '3000', 'trade_register' => '00123450000', 'cf' => 'OTHER', 'secret_info' => 'never-export-this', 'deleted_at' => null],
            ['id' => 4, 'company_name' => 'Deleted', 'name_acronym' => 'DEL', 'people_number' => '4000', 'trade_register' => '00444444444', 'cf' => 'CF-FOUR', 'secret_info' => 'never-export-this', 'deleted_at' => now()],
        ]);
    }

    private function signIn(): void
    {
        $user = new User;
        $user->id = 123;
        $this->actingAs($user, 'api');
    }

    public static function formats(): array
    {
        return [
            'excel' => ['excel', 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'csv' => ['csv', 'csv', 'text/csv; charset=UTF-8'],
            'pdf' => ['pdf', 'pdf', 'application/pdf'],
        ];
    }

    #[DataProvider('formats')]
    public function test_export_requires_authentication(string $type): void
    {
        $this->getJson('/api/v1/clients/export?exportType='.$type)->assertUnauthorized();
    }

    public static function invalidRequests(): array
    {
        return [
            'missing type' => [[], 'exportType'],
            'unsupported type' => [['exportType' => 'xlsx'], 'exportType'],
            'array type' => [['exportType' => ['csv']], 'exportType'],
            'bad client id' => [['exportType' => 'csv', 'clientId' => 'one'], 'clientId'],
            'array filter' => [['exportType' => 'csv', 'cf' => ['x']], 'cf'],
            'bad comparison' => [['exportType' => 'csv', 'peopleNumber' => 'invalid|1000'], 'peopleNumber'],
            'missing comparison value' => [['exportType' => 'csv', 'peopleNumber' => '>=|'], 'peopleNumber'],
        ];
    }

    #[DataProvider('invalidRequests')]
    public function test_invalid_requests_return_json_even_when_a_download_is_requested(array $query, string $field): void
    {
        $this->signIn();
        $this->withHeaders(['Accept' => 'text/csv'])
            ->get('/api/v1/clients/export?'.http_build_query($query))
            ->assertUnprocessable()->assertJsonValidationErrors($field);
    }

    public static function filters(): array
    {
        return [
            'all' => [[], [1, 2, 3]],
            'client' => [['clientId' => 2], [2]],
            'zero client means all' => [['clientId' => 0], [1, 2, 3]],
            'partial VAT' => [['tradeRegister' => '0012345'], [1, 3]],
            'partial CF' => [['cf' => 'CF-'], [1, 2]],
            'greater or equal' => [['peopleNumber' => '>=|2000'], [2, 3]],
            'greater' => [['peopleNumber' => '>|2000'], [3]],
            'less or equal' => [['peopleNumber' => '<=|2000'], [1, 2]],
            'less' => [['peopleNumber' => '<|2000'], [1]],
            'equal' => [['peopleNumber' => '=|2000'], [2]],
            'not equal' => [['peopleNumber' => '!=|2000'], [1, 3]],
            'alternate not equal' => [['peopleNumber' => '<>|2000'], [1, 3]],
            'legacy default' => [['peopleNumber' => '0'], [1, 2, 3]],
            'combined' => [['cf' => 'CF-', 'tradeRegister' => '0098', 'peopleNumber' => '>=|2000'], [2]],
            'empty filters' => [['clientId' => '', 'cf' => '', 'tradeRegister' => '', 'peopleNumber' => ''], [1, 2, 3]],
            'no matches' => [['clientId' => 999], []],
            'deleted client' => [['clientId' => 4], []],
        ];
    }

    #[DataProvider('filters')]
    public function test_export_and_list_apply_identical_filters(array $filters, array $expectedIds): void
    {
        $this->signIn();
        $list = $this->getJson('/api/v1/clients?'.http_build_query($filters + ['pageSize' => 100]))->assertOk();
        $this->assertSame($expectedIds, array_column($list->json('clientPage.clients'), 'clientId'));

        $response = $this->getJson('/api/v1/clients/export?'.http_build_query(
            $filters + ['exportType' => 'csv', 'page' => 99, 'pageSize' => 1]
        ))->assertOk();

        $rows = $this->csvRows($response->streamedContent());
        $this->assertSame(['Ragione sociale', 'Name Acronym', 'Numero Abitanti', 'P. iva'], array_shift($rows));
        $this->assertSame(DB::table('clients')->whereIn('id', $expectedIds)->orderBy('id')->pluck('company_name')->all(), array_column($rows, 0));
    }

    #[DataProvider('formats')]
    public function test_formats_download_directly_without_storing_export_files(string $type, string $extension, string $mime): void
    {
        $this->signIn();
        Storage::fake('local');
        Storage::fake('public');
        $temporaryPath = Storage::disk('local')->path('excel-temporary');
        config(['excel.temporary_files.local_path' => $temporaryPath]);

        $response = $this->withHeaders(['Origin' => 'https://frontend.example.test'])
            ->getJson('/api/v1/clients/export?'.http_build_query([
                'exportType' => $type, 'clientId' => 1, 'page' => 99, 'pageSize' => 1,
            ]))->assertOk()->assertDownload('Clienti.'.$extension)
            ->assertHeader('Content-Type', $mime)
            ->assertHeader('Access-Control-Expose-Headers', 'Content-Disposition');

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $content = $response->streamedContent();
        $this->assertStringNotContainsString('never-export-this', $content);
        $this->assertSame([], Storage::disk('local')->allFiles());
        $this->assertSame([], Storage::disk('public')->allFiles());
        $this->assertDirectoryDoesNotExist($temporaryPath);

        if ($type === 'csv') {
            $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
            $this->assertSame(['Alpha', 'ألفا', '1000', '00123456789'], $this->csvRows($content)[1]);
        } elseif ($type === 'excel') {
            $this->withWorkbook($content, function ($sheet) {
                $this->assertSame(2, $sheet->getHighestRow());
                $this->assertSame('ألفا', $sheet->getCell('B2')->getValue());
                $this->assertSame('00123456789', $sheet->getCell('D2')->getValue());
                $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('D2')->getDataType());
                $this->assertSame(1000, $sheet->getCell('C2')->getValue());
            });
        } else {
            $this->assertStringStartsWith('%PDF-', $content);
            $this->assertStringContainsString('%%EOF', $content);
            $this->assertStringContainsString('/Type /Page', $content);
        }
    }

    public function test_csv_exports_beyond_the_database_chunk_and_page_size(): void
    {
        $this->signIn();
        for ($id = 5; $id <= 1005; $id++) {
            DB::table('clients')->insert(['id' => $id, 'company_name' => 'Client '.$id]);
        }

        $rows = $this->csvRows($this->getJson('/api/v1/clients/export?exportType=csv&page=2&pageSize=1')
            ->assertOk()->streamedContent());
        $this->assertCount(1005, $rows);
        $this->assertSame('Client 1005', $rows[1004][0]);
    }

    #[DataProvider('formats')]
    public function test_empty_results_still_produce_a_valid_download(string $type): void
    {
        $this->signIn();
        $content = $this->getJson('/api/v1/clients/export?exportType='.$type.'&clientId=999')
            ->assertOk()->streamedContent();

        if ($type === 'csv') {
            $this->assertCount(1, $this->csvRows($content));
        } elseif ($type === 'excel') {
            $this->withWorkbook($content, function ($sheet) {
                $this->assertSame(1, $sheet->getHighestRow());
                $this->assertSame('Ragione sociale', $sheet->getCell('A1')->getValue());
            });
        } else {
            $this->assertStringStartsWith('%PDF-', $content);
            $this->assertStringContainsString('%%EOF', $content);
        }
    }

    public function test_csv_escapes_delimiters_quotes_newlines_and_formulas(): void
    {
        $this->signIn();
        DB::table('clients')->where('id', 1)->update([
            'company_name' => "Town, \"North\"\nOffice", 'name_acronym' => '=1+1', 'people_number' => '0',
        ]);
        $rows = $this->csvRows($this->getJson('/api/v1/clients/export?exportType=csv&clientId=1')
            ->assertOk()->streamedContent());
        $this->assertSame(["Town, \"North\"\nOffice", "'=1+1", '0', '00123456789'], $rows[1]);
    }

    public function test_excel_uses_literal_text_for_customer_values(): void
    {
        $this->signIn();
        DB::table('clients')->where('id', 1)->update(['company_name' => '=1+1', 'people_number' => '0']);
        $content = $this->getJson('/api/v1/clients/export?exportType=excel&pageSize=1')
            ->assertOk()->streamedContent();
        $this->withWorkbook($content, function ($sheet) {
            $this->assertSame(4, $sheet->getHighestRow());
            $this->assertSame('=1+1', $sheet->getCell('A2')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('A2')->getDataType());
            $this->assertSame(0, $sheet->getCell('C2')->getValue());
        });
    }

    private function csvRows(string $content): array
    {
        $stream = fopen('php://memory', 'w+b');
        fwrite($stream, substr($content, 3));
        rewind($stream);
        $rows = [];
        while (($row = fgetcsv($stream, 0, ',', '"', '')) !== false) {
            $rows[] = $row;
        }
        fclose($stream);

        return $rows;
    }

    private function withWorkbook(string $content, callable $assertions): void
    {
        // Only the test reader needs a file; the endpoint itself writes to the response.
        $path = tempnam(sys_get_temp_dir(), 'client-export-test-');
        $workbook = null;
        try {
            file_put_contents($path, $content);
            $workbook = IOFactory::load($path);
            $assertions($workbook->getActiveSheet());
        } finally {
            $workbook?->disconnectWorksheets();
            unlink($path);
        }
    }
}
