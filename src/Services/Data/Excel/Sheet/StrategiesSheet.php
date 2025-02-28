<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Governance\Domain\Entity\Strategy\Strategy;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class StrategiesSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId());
    }

    public function title(): string
    {
        return "Strategies";
    }

    protected function importRow(RowContext $row)
    {
        $strategy = Strategy::updateOrCreate([
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
        ], [
            'name' => $row[static::NAME_COLUMN]
        ]);

        $this->compositeKeyContainer->set('strategy', $strategy->scomp_id, $strategy->id);
    }
}

