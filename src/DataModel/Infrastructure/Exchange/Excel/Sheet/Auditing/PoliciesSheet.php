<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Auditing;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Policy;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class PoliciesSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const DESCRIPTION_COLUMN = 'description';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'description'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN));
    }

    public function title(): string
    {
        return "Policies";
    }

    protected function importRow(RowContext $row)
    {
        $policy = Policy::upsert(
            $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
                'name' => $row[static::NAME_COLUMN],
                'description' => $row[static::DESCRIPTION_COLUMN],
            ]
        );

        $this->compositeKeyContainer->set('policy', $policy->configurationItem->scomp_id, $policy->id);
    }
}
