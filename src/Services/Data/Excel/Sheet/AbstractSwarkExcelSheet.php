<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\Event;
use Maatwebsite\Excel\Row;
use Swark\Services\Data\CompositeKeyContainer;
use Swark\Services\Data\Concerns\WithSeparateHeaders;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

abstract class AbstractSwarkExcelSheet implements WithSeparateHeaders, WithHeadings, WithStartRow, OnEachRow, WithEvents, SkipsEmptyRows
{
    private array $delegatedListeners = [];

    public function __construct(public readonly CompositeKeyContainer $compositeKeyContainer)
    {
        $this->delegatedListeners[BeforeSheet::class] = [fn(BeforeSheet $event) => $this->beforeSheet($event)];
        $this->delegatedListeners[AfterSheet::class] = [fn(AfterSheet $event) => $this->afterSheet($event)];
    }

    public function headings(): array
    {
        $r = $this->header()->toArray();

        return $r;
    }

    public function startRow(): int
    {
        $r = sizeof($this->headings()) + 1;
        return $r;
    }


    private ?Header $header = null;

    public function header(): Header
    {
        if ($this->header == null) {
            $this->header = $this->createHeader();
        }

        return $this->header;
    }

    abstract function createHeader(): Header;

    private ?array $mappings = null;

    private function getMappings()
    {
        if ($this->mappings == null) {
            $this->mappings = $this->header()->columnKeyToIndex();
        }

        return $this->mappings;
    }

    public function onRow(Row $row)
    {
        $untilColumn = $this->header()->getMaxColumnWidth();

        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $data = [];

        foreach ($cellIterator as $cell) {
            $data[] = $cell->getValue();
        }

        $this->importRow(new RowContext($this->title(), $row->getRowIndex(), $row, $this->getMappings(), $data));
    }


    private function createDelegatedHandler(string $eventClazz): callable {
        return function(Event $event) use ($eventClazz) {
            if (isset($this->delegatedListeners[$eventClazz])) {
                foreach ($this->delegatedListeners[$eventClazz] as $listener) {
                    $listener($event);
                }
            }
        };
    }
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => $this->createDelegatedHandler(BeforeSheet::class ),
            AfterSheet::class => $this->createDelegatedHandler(AfterSheet::class)
        ];
    }

    public function onEvent(string $eventClazz, callable $function): AbstractSwarkExcelSheet {
        $this->delegatedListeners[$eventClazz][] = $function;
        return $this;
    }

    protected function beforeSheet(BeforeSheet $event)
    {

    }

    protected function afterSheet(AfterSheet $event)
    {
    }


    // return a list of integer IDs of scomps
    protected function getScomps(string $scompType, ?string $data): ?array
    {
        $data = trim($data ?? '');

        if (empty($data)) {
            return null;
        }

        $scomps = explode(",", $data);

        if (sizeof($scomps) == 0) {
            return null;
        }

        $r = [];

        foreach ($scomps as $scomp) {
            $scomp = trim($scomp);
            $r[] = $this->compositeKeyContainer->get($scompType, $scomp);
        }

        return $r;
    }

    protected function attachScomps(mixed $relationship, string $scompType, ?string $data): bool
    {
        $idsOfScomps = $this->getScomps($scompType, $data);

        if (null === $idsOfScomps) {
            return false;
        }

        $relationship->sync($idsOfScomps);

        return sizeof($idsOfScomps) > 0;
    }
}
