<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\System;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ResourceType;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Software;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;


class SystemsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';

    const DESCRIPTION_COLUMN = 'description';

    const STAGE_SCOMP_ID_COLUMN = 'stage_scomp-id';

    const ZONE_SCOMP_ID_COLUMN = 'zone_scomp_id';

    const BUSINESS_CRITICALITY_SCOMP_ID_COLUMN = "business_criticality";

    const INFRASTRUCTURE_CRITICALITY_SCOMP_ID_COLUMN = "infrastructure_criticality";

    const COMPOSED_OF_COLUMN = 'composed_of';


    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'stage-scomp-id', 'zone-scomp-id', 'criticality-scomp-id', 'criticality-scomp-id', 'composed-of-scomp-ids'];
    }

    public function title(): string
    {
        return "Systems";
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN))
            ->add(Column::of('Stage', static::STAGE_SCOMP_ID_COLUMN))
            ->add(Column::of('Zone', static::ZONE_SCOMP_ID_COLUMN))
            ->add(Column::of('Business criticality', static::BUSINESS_CRITICALITY_SCOMP_ID_COLUMN))
            ->add(Column::of('Infrastructure criticality', static::INFRASTRUCTURE_CRITICALITY_SCOMP_ID_COLUMN))
            ->add(Column::of('Composed of', static::COMPOSED_OF_COLUMN))
            ->next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('${stage.scomp_id}'))
            ->add(Column::of('${logical_zone.scomp_id}'))
            ->add(Column::of('${criticality.scomp_id}'))
            ->add(Column::of('${criticality.scomp_id}'))
            ->add(Column::of('${software.scomp_id | resource_type.scomp_id}*'));
    }

    protected function importRow(RowContext $row)
    {

        $system = System::upsert($row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
                'name' => $row->nonEmpty(static::NAME_COLUMN),
            ], [
                'description' => $row[static::DESCRIPTION_COLUMN],
                'stage_id' => $row->ifPresent(static::STAGE_SCOMP_ID_COLUMN, fn($scomp_id) => $this->compositeKeyContainer->get('stage', $scomp_id)) ?? null,
                'logical_zone_id' => $row->ifPresent(static::ZONE_SCOMP_ID_COLUMN, fn($scomp_id) => $this->compositeKeyContainer->get('logical_zone', $scomp_id)) ?? null,
                'business_criticality_id' => $row->ifPresent(static::BUSINESS_CRITICALITY_SCOMP_ID_COLUMN, fn($scomp_id) => $this->compositeKeyContainer->get('criticality', $scomp_id)) ?? null,
                'infrastructure_criticality_id' => $row->ifPresent(static::INFRASTRUCTURE_CRITICALITY_SCOMP_ID_COLUMN, fn($scomp_id) => $this->compositeKeyContainer->get('criticality', $scomp_id)) ?? null,
            ]);

        $this->compositeKeyContainer->set('system', key: $system->configurationItem->scomp_id, value: $system->id);

        $composedOfScompIds = $row[static::COMPOSED_OF_COLUMN];

        if (empty($composedOfScompIds)) {
            return;
        }

        $elements = explode(',', $composedOfScompIds);

        foreach ($elements as $element) {
            $element = trim($element);
            $parts = explode(":", $element);

            $refId = $this->compositeKeyContainer->get($parts[0], $parts[1]);

            if ($parts[0] == 'resource_type') {
                $system->resourceTypes()->attach(ResourceType::where('id', $refId)->firstOrFail());
            } elseif ($parts[0] == 'software') {
                $system->softwares()->attach(Software::where('id', $refId)->firstOrFail());
            } else {
                throw new \Exception("Unknown referenced element $element");
            }

            $system->save();
        }
    }
}
