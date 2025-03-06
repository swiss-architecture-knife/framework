<?php

namespace Swark\Services\Data\Excel;

use Illuminate\Contracts\Queue\ShouldQueue;
use InvalidArgumentException;
use Maatwebsite\Excel\ChunkReader;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Exceptions\NoTypeDetectedException;
use Maatwebsite\Excel\Factories\ReaderFactory;
use Maatwebsite\Excel\Reader;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;


/**
 * This custom reader orders the imports based upon their occurrence in the sheet registration and their actual sheet name
 */
class SwarkExcelReader extends Reader
{
    private function buildSheetsBasedUponTitle($import): array
    {
        $r = [];

        $sheetNames = $this->spreadsheet->getSheetNames();

        foreach ($import->sheets() as $sheetImport) {
            $name = is_object($sheetImport) ? get_class($sheetImport) : $sheetImport;
            throw_if(!($sheetImport instanceof WithTitle), "Sheet {$name} must implement WithTitle concern");

            $title = $sheetImport->title();

            $found = false;

            foreach ($sheetNames as $index => $sheetName) {
                if ($title == $sheetName) {
                    // it may be that one Excel sheet has multiple imports
                    $r[] = new SheetToImport($index, $sheetImport);
                    $found = true;
                }
            }

            throw_if(!$found, "Specified sheet '{$title}' does not exist");
        }

        return $r;
    }

    /**
     * Uses same logic as parent::loadSpreadsheet but replaces the buildSheets...() method
     * @param $import
     * @return void
     */
    public function loadSpreadsheet($import)
    {
        $this->readSpreadsheet();
        $this->sheetImports = $this->buildSheetsBasedUponTitle($import);

        // When no multiple sheets, use the main import object
        // for each loaded sheet in the spreadsheet
        if (!$import instanceof WithMultipleSheets) {
            $this->sheetImports = array_fill(0, $this->spreadsheet->getSheetCount(), $import);
        }

        $this->beforeImport($import);
    }


    /**
     * Custom reader logic to support sheet imports wher one sheet is imported mulitple times due to dependencies.
     *
     * @param object $import
     * @param string|UploadedFile $filePath
     * @param string|null $readerType
     * @param string|null $disk
     * @return \Illuminate\Foundation\Bus\PendingDispatch|$this
     *
     * @throws NoTypeDetectedException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws Exception
     */
    public function read($import, $filePath, ?string $readerType = null, ?string $disk = null)
    {
        $this->reader = $this->getReader($import, $filePath, $readerType, $disk);

        if ($import instanceof WithChunkReading) {
            return app(ChunkReader::class)->read($import, $this, $this->currentFile);
        }

        try {
            $this->loadSpreadsheet($import);

            ($this->transaction)(function () use ($import) {
                $sheetsToDisconnect = [];

                // change here to support multiple importers per Excel sheet
                /** @var SheetToImport $sheetToImport */
                foreach ($this->sheetImports as $sheetToImport) {
                    $sheetImport = $sheetToImport->sheet;
                    $index = $sheetToImport->indexOrNameOfSheet;

                    if ($excelSheet = $this->getSheet($import, $sheetImport, $index)) {
                        $excelSheet->import($sheetImport, $excelSheet->getStartRow($sheetImport));

                        // when using WithCalculatedFormulas we need to keep the sheet until all sheets are imported
                        if (!($sheetImport instanceof HasReferencesToOtherSheets)) {
                            $excelSheet->disconnect();
                        } else {
                            $sheetsToDisconnect[] = $excelSheet;
                        }
                    }
                }

                foreach ($sheetsToDisconnect as $excelSheet) {
                    $excelSheet->disconnect();
                }
            });

            $this->afterImport($import);
        } catch (Throwable $e) {
            $this->raise(new ImportFailed($e));
            $this->garbageCollect();
            throw $e;
        }

        return $this;
    }

    /**
     * @param object $import
     * @param string|UploadedFile $filePath
     * @param string|null $readerType
     * @param string $disk
     * @return IReader
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws NoTypeDetectedException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws InvalidArgumentException
     */
    private function getReader($import, $filePath, ?string $readerType = null, ?string $disk = null): IReader
    {
        $shouldQueue = $import instanceof ShouldQueue;
        if ($shouldQueue && !$import instanceof WithChunkReading) {
            throw new InvalidArgumentException('ShouldQueue is only supported in combination with WithChunkReading.');
        }

        if ($import instanceof WithEvents) {
            $this->registerListeners($import->registerEvents());
        }

        if ($import instanceof WithCustomValueBinder) {
            Cell::setValueBinder($import);
        }

        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $temporaryFile = $shouldQueue ? $this->temporaryFileFactory->make($fileExtension) : $this->temporaryFileFactory->makeLocal(null, $fileExtension);
        $this->currentFile = $temporaryFile->copyFrom(
            $filePath,
            $disk
        );

        return ReaderFactory::make(
            $import,
            $this->currentFile,
            $readerType
        );
    }

    /**
     * Garbage collect.
     */
    private function garbageCollect()
    {
        $this->clearListeners();
        $this->setDefaultValueBinder();

        // Force garbage collecting
        /* @phpstan-ignore unset.possiblyHookedProperty */
        unset($this->sheetImports);
        /* @phpstan-ignore unset.possiblyHookedProperty */
        unset($this->spreadsheet);

        $this->currentFile->delete();
    }
}

readonly class SheetToImport
{
    public function __construct(public int|string $indexOrNameOfSheet, public object $sheet)
    {
    }
}
