<?php

namespace Swark\Services\Data\Excel\Sheet;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Swark\DataModel\Ecosystem\Domain\Entity\Relationship;
use Swark\DataModel\Ecosystem\Domain\Entity\RelationshipType;
use Swark\DataModel\Enterprise\Domain\Entity\Zone;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class LogicalZonesSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const DESCRIPTION_COLUMN = 'description';
    const DATA_CLASSIFICATION_SCOMP_ID_COLUMN = 'data_classification_scomp_id';
    const ACTORS_SCOMP_ID_COLUMN = 'actors_scomp_id';

    const ALLOW_TARGET_ZONES_COLUMN = 'allow_zone_scomp_ids';
    const DENY_TARGET_ZONES_COLUMN = 'deny_zone_scomp_ids';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'description', 'data-classification-scomp-id', 'actors-scomp-id', 'allow-zone-scomp-ids', 'deny-zone-scomp-ids'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN))
            ->add(Column::of('Data_classification::scomp_id', static::DATA_CLASSIFICATION_SCOMP_ID_COLUMN))
            ->add(Column::of('Actors::scomp-id', static::ACTORS_SCOMP_ID_COLUMN))
            ->add(Column::of('Allow access to', static::ALLOW_TARGET_ZONES_COLUMN))
            ->add(Column::of('Deny access to (default: deny all)', static::DENY_TARGET_ZONES_COLUMN))
        ->next()
            ->add(Column::empty(3))
            ->add(Column::of('${data_classification.scomp_id}'))
            ->add(Column::of('${actor.scomp_id}*'))
            ->add(Column::of('${logical_zone.scomp_id}*'))
            ->add(Column::of('${logical_zone.scomp_id}*'))

            ;
    }

    public function title(): string
    {
        return "Logical zones";
    }

    private ?RelationshipType $allowRelationshipType;
    private ?RelationshipType $denyRelationshipType;

    protected function beforeSheet(BeforeSheet $event)
    {
        $this->allowRelationshipType = RelationshipType::updateOrCreate(['scomp_id' => 'allow_access'],
            ['name' => 'Allow access', 'source_name' => 'Source', 'target_name' => 'Target']);
        $this->denyRelationshipType = RelationshipType::updateOrCreate(['scomp_id' => 'deny_access'],
            ['name' => 'Deny access', 'source_name' => 'Source', 'target_name' => 'Target']);
    }

    protected array $processedRows = [];

    protected function importRow(RowContext $row)
    {
        $dataClassificationScompId = $row[static::DATA_CLASSIFICATION_SCOMP_ID_COLUMN];

        $logicalZone = Zone::updateOrCreate([
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
        ], [
            'name' => $row[static::NAME_COLUMN],
            'description' => $row[static::DESCRIPTION_COLUMN],
            'data_classification_id' => !empty($dataClassificationScompId) ? $this->compositeKeyContainer->get('data_classification', $dataClassificationScompId) : null
        ]);

        if ($this->attachScomps($logicalZone->actors(), 'actor', $row[static::ACTORS_SCOMP_ID_COLUMN])) {
            $logicalZone->save();
        }

        $this->compositeKeyContainer->set('logical_zone', $logicalZone->scomp_id, $logicalZone->id);
        $this->compositeKeyContainer->set('logical_zone', Str::upper($logicalZone->scomp_id), $logicalZone->id);
        $this->processedRows[] = $row;
    }

    /**
     * We have to update all relationships between zones after having them added.
     * @param AfterSheet $event
     * @return void
     */
    protected function afterSheet(AfterSheet $event)
    {
        // 2nd pass: update relationships
        foreach ($this->processedRows as $row) {
            $sourceZoneId = $this->compositeKeyContainer->get('logical_zone', $row[Column::SCOMP_ID_COLUMN]);
            $allowTargetZoneIds = $this->getScomps('logical_zone', $row[static::ALLOW_TARGET_ZONES_COLUMN]) ?? [];
            $denyTargetZoneIds = $this->getScomps('logical_zone', $row[static::DENY_TARGET_ZONES_COLUMN]) ?? [];

            foreach ($allowTargetZoneIds as $allowTargetZoneId) {
                Relationship::updateOrCreate([
                    'source_type' => 'logical_zone', 'source_id' => $sourceZoneId, 'target_type' => 'logical_zone', 'target_id' => $allowTargetZoneId,
                    'relationship_type_id' => $this->allowRelationshipType->id
                ]);
            }

            foreach ($denyTargetZoneIds as $denyTargetZoneId) {
                Relationship::updateOrCreate([
                    'source_type' => 'logical_zone', 'source_id' => $sourceZoneId, 'target_type' => 'logical_zone', 'target_id' => $denyTargetZoneId,
                    'relationship_type_id' => $this->denyRelationshipType->id
                ]);
            }
        }
    }
}

