<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel;

use Swark\DataModel\Infrastructure\Exchange\CompositeKeyContainer;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class DataModelExcelFile
{
    private array $dependencies = [];

    public function __construct(
        public readonly CompositeKeyContainer $compositeKeyContainer,
        private readonly array                $swarkExcelSheetClasses
    )
    {
        foreach ($this->swarkExcelSheetClasses as $clazzName) {
            $this->dependencies[$clazzName] = $this->register($clazzName);
        }
    }

    private function register(string $clazzName): AbstractSwarkExcelSheet
    {
        return new $clazzName($this->compositeKeyContainer);
    }

    public function all(): array
    {
        return collect($this->dependencies)->toArray();
    }

    public function resolve($clazzName): AbstractSwarkExcelSheet
    {
        if (!isset($this->dependencies[$clazzName])) {
            throw new \Exception("Excel sheet {$clazzName} not registered");
        }

        return $this->dependencies[$clazzName];
    }
}
