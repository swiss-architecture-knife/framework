<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Deployment\Domain\Entity\Stage;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

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
        $stage = Stage::updateOrCreate([
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
        ], [
            'name' => $row->nonEmpty(static::NAME_COLUMN)
        ]);

        $this->compositeKeyContainer->set('stage', $stage->scomp_id, $stage->id);
    }
}

