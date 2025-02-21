<?php

namespace Swark\Tests\Integration;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Reader;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PHPUnit\Framework\Attributes\Test;
use Swark\Services\Data\Concerns\WithSeparateHeaders;
use Swark\Services\Data\Excel\Export\SwarkExport;
use Swark\Services\Data\Excel\Import\SwarkExcelImport;
use Swark\Tests\IntegrationTestCase;

class ExportIntegrationTest extends IntegrationTestCase
{
    // TODO
    #[Test]
    public function exporting_excel_files(): void
    {
        $this->markTestSkipped('must be revisited.');

        Sheet::listen(AfterSheet::class, function (AfterSheet $sheet) {

            if ($sheet->getConcernable() instanceof WithSeparateHeaders) {
                $header = $sheet->getConcernable()->header();

                $colors = ['808080', 'A9A9A9', 'B2BEB5', 'D3D3D3'];
                $rowIdxStartWithZero = 0;

                foreach ($header->eachRow() as $row) {
                    $useColor = $colors[min($rowIdxStartWithZero, sizeof($colors) - 1)];
                    $colName = \Swark\Services\Data\Excel\Header::numToAlpha($row->width());
                    $targetRange = 'A' . ($rowIdxStartWithZero + 1) . ':' . $colName . ($rowIdxStartWithZero + 1);

                    $style =
                        $sheet->getSheet()->getStyle($targetRange);

                    $style
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($useColor);

                    $style->getFont()->setBold(true);

                    $rowIdxStartWithZero++;
                }

            }

            // $sheet->sheet->setMergeCells(['A1:A2']);
            $sheet->sheet->getStyle('A3:A4')->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => [
                            'rgb' => 'FF0000'
                        ]
                    ],
                ],
            ]);
        });

        Excel::store(new SwarkExport(), 'test.xlsx');
    }
}
