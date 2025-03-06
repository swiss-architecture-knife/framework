<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Business;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Actor;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class ActorsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows
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
        return "Actors";
    }

    protected function importRow(RowContext $row)
    {
        $actor = Actor::upsert(
            $row->nonEmpty(Column::SCOMP_ID_COLUMN), [
            'name' => $row[static::NAME_COLUMN],
        ]);

        $this->compositeKeyContainer->set('actor', $actor->configurationItem->scomp_id, $actor->id);
    }
}

