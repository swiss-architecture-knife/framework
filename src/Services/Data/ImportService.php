<?php

namespace Swark\Services\Data;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Reader;
use Swark\DataModel\Policy\Domain\Entity\Scope;
use Swark\Services\Data\Excel\Import\SwarkExcelImport;
use Swark\Services\Data\Excel\SwarkExcelReader;

class ImportService
{
    /**
     * Import Excel and markdown files into the database
     *
     * @param string $path
     * @param array $withOptions
     * @return void
     */
    public function import(
        string $path,
        array  $withOptions = [],
    )
    {
        // bind our own reader instance so that we can import Excel sheets based upon their name and in the right order
        app()->bind(Reader::class, SwarkExcelReader::class);

        $importOptions = new ImportOptions($path, $withOptions);

        $excelFile = $importOptions->excelFilePath();

        // first, import the Excel file, so we have all relevant scomp IDs
        if ($excelFile->isFile()) {
            yo_info("Excel file %s exists, trying to import", [$excelFile], 'import.excel_exists');

            $swarkExcelImporter = new SwarkExcelImport(options: $importOptions);

            Excel::import(
                $swarkExcelImporter,
                $excelFile->getRealPath()
            );
        } else {
            yo_warn("Excel file %s dos not exist, skipping", [$excelFile], 'import.excel_missing');
        }

        // second, import all local filers
        $directoryImporter = new Filesystem\Import\SwarkFilesystemImport(
            options: $importOptions,
        );

        $directoryImporter->import();

        // at last, update the rules
        if ($importOptions->has('rules')) {
            $scopes = Scope::all();

            yo_info('Importing scopes', [$scopes], 'import.scopes');

            foreach ($scopes as $scope) {
                $scope->detect();
            }
        }
    }
}


