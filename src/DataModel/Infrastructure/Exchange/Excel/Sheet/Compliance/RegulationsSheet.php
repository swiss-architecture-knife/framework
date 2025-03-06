<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Compliance;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Regulation;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class RegulationsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
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
        return "Regulations";
    }

    protected function importRow(RowContext $row)
    {
        /** @var Regulation $regulation */
        $regulation = Regulation::upsert($row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
                'name' => $row[static::NAME_COLUMN]
            ]
        );

        $this->compositeKeyContainer->set('regulation', $regulation->configurationItem->scomp_id, $regulation->id);
    }
}

