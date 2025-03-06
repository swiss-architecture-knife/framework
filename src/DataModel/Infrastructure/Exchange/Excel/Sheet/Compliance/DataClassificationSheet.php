<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Compliance;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\DataClassification;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;


class DataClassificationSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const DESCRIPTION_COLUMN = 'description';
    const NAME_COLUMN = 'name';

    public function generator(): \Generator
    {
        yield ['private', 'private-id', 'Private data classification'];
    }

    public function title(): string
    {
        return "Data classifications";
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', 'name'))
            ->add(Column::scompId())
            ->add(Column::of('Description', 'description'));
    }

    protected function importRow(RowContext $row)
    {
        $dataClassification = DataClassification::upsert($row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
                'name' => $row[static::NAME_COLUMN],
                'description' => $row[static::DESCRIPTION_COLUMN],
            ]);

        $this->compositeKeyContainer->set('data_classification', $dataClassification->configurationItem->scomp_id, $dataClassification->id);
    }
}
