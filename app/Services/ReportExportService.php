<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    public function streamCsv(array $headers, iterable $rows, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function streamPdf(string $title, array $lines, string $filename): StreamedResponse
    {
        $content = $this->buildSimplePdf($title, $lines);

        return response()->streamDownload(function () use ($content): void {
            echo $content;
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    private function buildSimplePdf(string $title, array $lines): string
    {
        $y = 800;
        $lineHeight = 18;
        $textObjects = [];

        $escape = fn (string $text) => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);

        $textObjects[] = sprintf("BT /F1 16 Tf 50 %d Td (%s) Tj ET", $y, $escape($title));
        $y -= $lineHeight * 1.5;

        foreach ($lines as $line) {
            $textObjects[] = sprintf("BT /F1 12 Tf 50 %d Td (%s) Tj ET", $y, $escape((string) $line));
            $y -= $lineHeight;
        }

        $streamContent = implode("\n", $textObjects);
        $length = strlen($streamContent);

        $pdf = "%PDF-1.4\n";
        $pdf .= "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n";
        $pdf .= "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n";
        $pdf .= "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n";
        $pdf .= "4 0 obj << /Length {$length} >> stream\n{$streamContent}\nendstream endobj\n";
        $pdf .= "5 0 obj << /Type /Font /Subtype /Type1 /Name /F1 /BaseFont /Helvetica >> endobj\n";
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";

        $offsets = [];
        $pos = strlen("%PDF-1.4\n");
        foreach ([
            "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n",
            "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n",
            "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n",
            "4 0 obj << /Length {$length} >> stream\n{$streamContent}\nendstream endobj\n",
            "5 0 obj << /Type /Font /Subtype /Type1 /Name /F1 /BaseFont /Helvetica >> endobj\n",
        ] as $part) {
            $offsets[] = $pos;
            $pos += strlen($part);
        }

        foreach ($offsets as $offset) {
            $pdf .= str_pad((string) $offset, 10, '0', STR_PAD_LEFT).' 00000 n '."\n";
        }

        $pdf .= "trailer << /Size 6 /Root 1 0 R >>\nstartxref\n".$pos."\n%%EOF";

        return $pdf;
    }
}
