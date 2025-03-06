<?php

namespace Swark\DataModel\Application\Exchange;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Reader;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Scope;
use Swark\DataModel\Infrastructure\Exchange\Excel\DataModelExcelFileFactory;
use Swark\DataModel\Infrastructure\Exchange\Excel\Export\DataModelExcelExport;
use Swark\DataModel\Infrastructure\Exchange\Excel\Import\DataModelExcelImport;
use Swark\DataModel\Infrastructure\Exchange\Excel\Import\DataModelImportOptions;
use Swark\DataModel\Infrastructure\Exchange\Excel\OrderedSheetExcelReader;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Concerns\WithSeparateHeaders;
use Swark\DataModel\Infrastructure\Exchange\Filesystem\DataModelFilesystemImporter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


readonly class DataModelExchangeService
{
    public function __construct(public readonly DataModelExcelFileFactory $dataModelExcelFileFactory)
    {
    }

    /**
     * Export Excel data model template
     *
     * @param string|null $filename
     * @param string|null $writerType
     * @param array $headers
     * @return BinaryFileResponse
     */
    public function downloadDataModelTemplate(?string $filename = 'swark_datamodel.xlsx', null|string $writerType = null, array $headers = []): BinaryFileResponse
    {
        $exportableSheets = $this->dataModelExcelFileFactory->createForExport()->all();

        return Excel::download(new DataModelExcelExport($exportableSheets), $filename, $writerType, $headers);
    }

    /**
     * Import Excel and markdown files into the database
     *
     * @param string $path
     * @return void
     */
    public function import(DataModelImportOptions $importOptions): void
    {
        $this->importExcel($importOptions);
        $this->importMarkdowns($importOptions);
        $this->postImport($importOptions);
    }

    protected function importExcel(DataModelImportOptions $importOptions): void
    {
        if ($importOptions->markdownOnly) {
            return;
        }

        // bind our own reader instance so that we can import Excel sheets based upon their name and in the right order
        app()->bind(Reader::class, OrderedSheetExcelReader::class);

        $excelFile = $importOptions->excelFilePath();

        // first, import the Excel file, so we have all relevant scomp IDs
        if (!$excelFile->isFile()) {
            yo_warn("Excel file %s dos not exist, skipping", [$excelFile], 'import.excel_missing');
            return;
        }

        yo_info("Excel file %s exists, trying to import", [$excelFile], 'import.excel_exists');

        $swarkExcelImporter = new DataModelExcelImport(
            dataModelExcelFile: $this->dataModelExcelFileFactory->createForImport(),
            options: $importOptions
        );

        Excel::import(
            $swarkExcelImporter,
            $excelFile->getRealPath()
        );
    }

    /**
     * @throws \Exception
     */
    protected function importMarkdowns(DataModelImportOptions $importOptions): void
    {
        // second, import all local filers
        $directoryImporter = new DataModelFilesystemImporter(
            rootDirectory: $importOptions->rootDirectory(),
            enableRegulations: $importOptions->isCategoryEnabled(DataModelImportOptions::COMPLIANCE),
        );

        $directoryImporter->import();
    }

    protected function postImport(DataModelImportOptions $importOptions)
    {
        // at last, update the rules
        if (!$importOptions->isCategoryEnabled(DataModelImportOptions::COMPLIANCE)) {
            return;
        }

        $scopes = Scope::all();

        yo_info('Updating scopes', [$scopes], 'import.scopes');

        foreach ($scopes as $scope) {
            $scope->detect();
        }
    }
}
