<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Operations;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\Stage;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class StagesSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows
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
        return "Stages";
    }

    protected function importRow(RowContext $row)
    {
        $stage = Stage::upsert(
            $row->nonEmpty(Column::SCOMP_ID_COLUMN),
        [
            'name' => $row->nonEmpty(static::NAME_COLUMN)
        ]);

        $this->compositeKeyContainer->set('stage', $stage->configurationItem->scomp_id, $stage->id);
    }
}

