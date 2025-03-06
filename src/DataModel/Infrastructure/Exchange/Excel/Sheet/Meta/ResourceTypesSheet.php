<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Meta;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ResourceType;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;


class ResourceTypesSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const TECHNOLOGY_SCOMP_ID = 'technology_scomp_id';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'technology-scomp-id'];
    }

    public function title(): string
    {
        return "Resource types";
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Technology::scomp_id', static::TECHNOLOGY_SCOMP_ID))
            ->next()
            ->add(Column::empty(2))
            ->add(Column::of('${technology.scomp_id}?'));
    }

    protected function importRow(RowContext $row)
    {
        $args = [];

        $technologyScompId = $row[static::TECHNOLOGY_SCOMP_ID];

        if (!empty($technologyScompId)) {
            $args = [
                'technology_version_id' => $this->compositeKeyContainer->get('technology_version', $row[static::TECHNOLOGY_SCOMP_ID] . ':latest'),
            ];
        }

        $resourceType = ResourceType::upsert(
            $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
                'name' => $row->nonEmpty(static::NAME_COLUMN),
            ], $args);

        $this->compositeKeyContainer->set('resource_type', key: $resourceType->configurationItem->scomp_id, value: $resourceType->id);
    }
}
