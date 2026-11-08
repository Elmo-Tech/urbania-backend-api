<?php

namespace App\Services\Client;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TCPDF;

class ClientExportService
{
    private const HEADINGS = ['Ragione sociale', 'Name Acronym', 'Numero Abitanti', 'P. iva'];

    public function download(Builder $query, string $exportType): StreamedResponse
    {
        [$extension, $contentType] = match ($exportType) {
            'excel' => ['xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'csv' => ['csv', 'text/csv; charset=UTF-8'],
            'pdf' => ['pdf', 'application/pdf'],
        };

        return response()->streamDownload(function () use ($query, $exportType) {
            $clients = (clone $query)
                ->select(['id', 'company_name', 'name_acronym', 'people_number', 'trade_register'])
                ->orderBy('id')
                ->lazy(1000);

            match ($exportType) {
                'excel' => $this->writeExcel($clients),
                'csv' => $this->writeCsv($clients),
                'pdf' => $this->writePdf($clients),
            };
        }, "Clienti.{$extension}", [
            'Content-Type' => $contentType,
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function row(Client $client): array
    {
        return [
            (string) $client->company_name,
            (string) $client->name_acronym,
            (string) $client->people_number,
            (string) $client->trade_register,
        ];
    }

    private function writeCsv(iterable $clients): void
    {
        $output = fopen('php://output', 'wb');

        try {
            // Excel recognizes UTF-8 reliably when the CSV includes a BOM.
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, self::HEADINGS, ',', '"', '', "\r\n");

            foreach ($clients as $client) {
                $row = array_map(function (string $value): string {
                    // CSV has no cell types: neutralize spreadsheet formulas in text.
                    return preg_match('/^[\x00-\x20]*[=+@-]/u', $value) ? "'".$value : $value;
                }, $this->row($client));
                fputcsv($output, $row, ',', '"', '', "\r\n");
            }
        } finally {
            fclose($output);
        }
    }

    private function writeExcel(iterable $clients): void
    {
        $spreadsheet = new Spreadsheet;

        try {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Clienti');
            $sheet->fromArray(self::HEADINGS);
            $sheet->freezePane('A2');
            $sheet->getStyle('A1:D1')->getFont()->setBold(true);
            $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E5E7EB');

            foreach (['A' => 55, 'B' => 25, 'C' => 22, 'D' => 24] as $column => $width) {
                $sheet->getColumnDimension($column)->setWidth($width);
            }

            $rowNumber = 2;
            foreach ($clients as $client) {
                foreach ($this->row($client) as $index => $value) {
                    // Explicit text types preserve leading zeros and prevent formulas.
                    $type = $index === 2 && ctype_digit($value) ? DataType::TYPE_NUMERIC : DataType::TYPE_STRING;
                    $sheet->setCellValueExplicit([$index + 1, $rowNumber], $value, $type);
                }
                $rowNumber++;
            }

            $sheet->setAutoFilter('A1:D'.($rowNumber - 1));
            $sheet->getStyle('A1:D'.($rowNumber - 1))->getAlignment()->setWrapText(true);

            // Bypass Laravel Excel's temporary-file download workflow entirely.
            $writer = new Xlsx($spreadsheet);
            $writer->setUseDiskCaching(false);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    private function writePdf(iterable $clients): void
    {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('Clienti');
        $pdf->setPrintHeader(false);
        $pdf->setFooterFont(['dejavusans', '', 8]);
        $pdf->SetFooterMargin(8);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->AddPage();

        $widths = [45, 20, 17, 18];
        $html = '<table border="1" cellpadding="5"><thead><tr bgcolor="#E5E7EB">';
        foreach (self::HEADINGS as $index => $heading) {
            $html .= '<th width="'.$widths[$index].'%"><b>'.$heading.'</b></th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($clients as $client) {
            $html .= '<tr nobr="true">';
            foreach ($this->row($client) as $index => $value) {
                $html .= '<td width="'.$widths[$index].'%">'
                    .htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</td>';
            }
            $html .= '</tr>';
        }

        $pdf->writeHTML($html.'</tbody></table>');
        // S returns the PDF bytes in memory; no export file is written to disk.
        echo $pdf->Output('', 'S');
    }
}
