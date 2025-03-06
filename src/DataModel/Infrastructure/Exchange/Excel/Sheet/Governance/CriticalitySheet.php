<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Governance;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Criticality;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class CriticalitySheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const POSITION_COLUMN = 'type';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', '1'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('position', static::POSITION_COLUMN));
    }


    public function title(): string
    {
        return "Criticality";
    }

    protected function importRow(RowContext $row)
    {
        $position = $row->rowNumber;

        $criticality = Criticality::upsert($row->nonEmpty(1),
         [
            'name' => $row[0],
            'position' => $row->ifPresent(2, fn($value) => (int)$value) ?? $position
        ]);

        $this->compositeKeyContainer->set('criticality', $criticality->configurationItem->scomp_id, $criticality->id);
    }
}

