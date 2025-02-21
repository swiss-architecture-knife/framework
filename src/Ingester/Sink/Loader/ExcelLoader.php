<?php
declare(strict_types=1);

namespace Swark\Ingester\Sink\Loader;

use Illuminate\Contracts\Filesystem\Filesystem;
use League\Csv\Statement;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Swark\Ingester\IngesterException;
use Swark\Ingester\Model\Context;
use Swark\Ingester\Model\Relationship\Attribute;
use Swark\Ingester\Model\StatusFlag;
use Swark\Ingester\Sink\Structure\Column;
use Swark\Ingester\Sink\Structure\ColumnMapping;
use Swark\Ingester\Sink\Uniqueness\CompoundIdentifier;
use Swark\Ingester\Sink\Uniqueness\CompoundIdentifiersRepository;
use Swark\Ingester\Sink\Uniqueness\Identifier;

class ExcelLoader implements CompoundIdentifiersRepository, ItemDataProvider
{
    const ITEMS_CSV = 'items.xlsx';

    public function __construct(
        public readonly Context    $context,
        public readonly Filesystem $excelPath,
        public readonly array      $options = [],
    )
    {
    }

    private ?bool $valid = null;

    private ?Xlsx $reader = null;

    public function assertUsable(): void
    {
        if ($this->valid === null) {
            $this->valid = true;

            if (!$this->excelPath->exists($this->relativePath())) {
                $this->context->unusableBecause(StatusFlag::EXCEL_CSV_MISSING);
                $this->valid = false;
            }
        }

        if (!$this->valid) {
            throw new IngesterException(StatusFlag::ITEMS_CSV_PRECONDITION_FAILED);
        }
    }

    private function relativePath(): string
    {
        return $this->context->alias . '/' . self::ITEMS_CSV;
    }

    private ?ColumnMapping $columnMapping = null;

    private function hasCorrespondingAttribute(string $columnName): ?Attribute
    {
        $candidates = [$columnName, Column::toDefaultAttributeName($columnName)];

        foreach ($candidates as $candidate) {
            if ($attribute = $this->context->attributes()->get($candidate)) {
                return $attribute;
            }
        }

        return null;
    }

    private function mergeHeadersWithConfiguration(array $autodetectedHeaders): array
    {
        if (isset($this->options['map'])) {
            foreach ($this->options['map'] as $columnName => $attributeName) {
                if (!($attribute = $this->hasCorrespondingAttribute($attributeName))) {
                    throw new IngesterException(StatusFlag::COLUMN_MAPPER_INVALID, "csv.map.$columnName is invalid: attribute $attributeName does not exist");
                }
                $autodetectedHeaders[$columnName] = $attribute;
            }
        }

        return $autodetectedHeaders;
    }

    private function reader(): Xlsx
    {
        if ($this->reader == null) {
            $this->assertUsable();

            $this->reader = new Xlsx();
            $this->reader->setReadDataOnly(true);

            $this->columnMapping = new ColumnMapping($this->context->attributes());

            $spreadsheet = $this->reader->load($this->excelPath->path($this->relativePath()));
            $worksheet = $spreadsheet->getActiveSheet();
            $isheader = 1;
            $useHeaders = [];

            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $row = [];
                foreach ($cellIterator as $cell) {
                    $row[] = $cell->getValue();
                }

                if ($isheader) {
                    foreach ($row as $idx => $columnName) {
                        if ($attribute = $this->hasCorrespondingAttribute($columnName)) {
                            $useHeaders[$columnName] = $attribute;
                        }
                    }

                    var_dump($row);
                    $isheader = 0;
                }
            }

            var_dump($useHeaders);

            try {
                // automatic configuration
                $useHeaders = $this->mergeHeadersWithConfiguration($useHeaders);

                foreach ($useHeaders as $columnName => $attribute) {
                    $this->columnMapping->map($columnName, $attribute->name);
                }
            } catch (\Exception $e) {
                throw new IngesterException(StatusFlag::ITEMS_CSV_INVALID, $e->getMessage());
            }
        }

        return $this->reader;
    }

    public function columnMapping(): ColumnMapping
    {
        $this->reader();
        return $this->columnMapping;
    }

    public function findCompoundIdentifiers(): array
    {
        $r = [];

        foreach ($this->reader()->skipEmptyRecords()->getRecords() as $offset => $line) {
            $uniques = new CompoundIdentifier();

            /** @var Attribute $uniqueAttribute */
            foreach ($this->context->uniqueAttributes() as $uniqueAttribute) {
                $value = $uniqueAttribute->converter()->convert($line[$this->columnMapping->getColumnByAttribute($uniqueAttribute)->name], $uniqueAttribute);
                $uniques->add(new Identifier($value, $uniqueAttribute));
            }

            // ($offset - 1) because we have to skip the header
            $uniques->setContext('CSV_OFFSET', ($offset - 1));
            $r[] = $uniques;
        }

        return $r;
    }

    public function upsertItem(CompoundIdentifier $compoundIdentifier, array $mapAttributeToRawValue): array
    {
        $offset = $compoundIdentifier->getContext('CSV_OFFSET');

        $record = Statement::create()
            ->offset($offset)
            ->limit(1)->process($this->reader())->first();

        /** @var Attribute $attribute */
        foreach ($this->context->attributes() as $attribute) {
            $mapAttributeToRawValue[$attribute->name] = $record[$this->columnMapping->getColumnByAttribute($attribute)->name] ?? null;
        }

        return $mapAttributeToRawValue;
    }
}
