<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Export;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Concerns\WithSeparateHeaders;


readonly class DataModelExcelExport implements WithMultipleSheets, WithEvents
{
    public function __construct(private array $instances)
    {
    }

    public function sheets(): array
    {
        return $this->instances;
    }

    public function registerEvents(): array
    {
        // it is a little bit unfortunate, but we cannot register AfterSheet in the return but have to use the global listener
        Sheet::listen(AfterSheet::class, function (AfterSheet $sheet) {
            if (!($sheet->getConcernable() instanceof WithSeparateHeaders)) {
                return;
            }

            $header = $sheet->getConcernable()->header();

            $colors = ['808080', 'A9A9A9', 'B2BEB5', 'D3D3D3'];
            $rowIdxStartWithZero = 0;

            foreach ($header->eachRow() as $row) {
                $useColor = $colors[min($rowIdxStartWithZero, sizeof($colors) - 1)];
                $colName = \Swark\DataModel\Infrastructure\Exchange\Excel\Header::numToAlpha($row->width());
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
        });

        return [];
    }
}
