<?php

namespace Swark\Tests\Integration\DataModel\Exchange;

use Illuminate\Contracts\Filesystem\Factory;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Attributes\Test;
use Swark\DataModel\Infrastructure\Exchange\Excel\DataModelExcelFileFactory;
use Swark\DataModel\Infrastructure\Exchange\Excel\Export\DataModelExcelExport;
use Swark\Tests\IntegrationTestCase;

class ExportIntegrationTest extends IntegrationTestCase
{
    #[Test]
    public function excelTemplate_canBeGenerated(): void
    {
        // given
        $file = __DIR__ . '/export.xlsx';
        $pathToFileInDisk = app(Factory::class)->disk()->path($file);
        $exportableSheets = app()->make(DataModelExcelFileFactory::class)->createForExport()->all();

        // when
        Excel::store(new DataModelExcelExport($exportableSheets), $file);

        // then
        // we must have at least 10 sheets to exported
        $this->assertGreaterThan(10, sizeof($exportableSheets));
        // file must be exported
        $this->assertFileExists($pathToFileInDisk);
        // raw loading of exported Excel file
        $exportedSpreadsheetFile = IOFactory::load($pathToFileInDisk);
        // has same count of exported sheets?
        $this->assertEquals(sizeof($exportableSheets), $exportedSpreadsheetFile->getSheetCount());
    }
}
