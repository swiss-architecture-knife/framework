<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Policy\Domain\Entity\Policy;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

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
        $policy = Policy::updateOrCreate([
            'scomp_id' => $row[Column::SCOMP_ID_COLUMN],
        ], [
            'name' => $row[static::NAME_COLUMN],
            'description' => $row[static::DESCRIPTION_COLUMN],
        ]);

        $this->compositeKeyContainer->set('policy', $policy->scomp_id, $policy->id);
    }
}
