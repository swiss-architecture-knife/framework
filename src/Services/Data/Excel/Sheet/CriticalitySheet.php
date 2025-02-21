<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Enterprise\Domain\Entity\Criticality;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

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

        $criticality = Criticality::updateOrCreate([
            'scomp_id' => $row->nonEmpty(1),
        ], [
            'name' => $row[0],
            'position' => $row->ifPresent(2, fn($value) => (int)$value) ?? $position
        ]);

        $this->compositeKeyContainer->set('criticality', $criticality->scomp_id, $criticality->id);
    }
}

