<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Enterprise\Domain\Entity\DataClassification;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;


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
        $dataClassification = DataClassification::updateOrCreate([
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
        ], [
            'name' => $row[static::NAME_COLUMN],
            'description' => $row[static::DESCRIPTION_COLUMN],
        ]);

        $this->compositeKeyContainer->set('data_classification', $dataClassification->scomp_id, $dataClassification->id);
    }
}
