<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Events\BeforeSheet;
use Swark\DataModel\Ecosystem\Domain\Entity\Relationship;
use Swark\DataModel\Ecosystem\Domain\Entity\RelationshipType;
use Swark\DataModel\Ecosystem\Domain\Entity\ResourceType;
use Swark\DataModel\Infrastructure\Domain\Entity\Resource;
use Swark\Services\Data\Concerns\HasPublicTitle;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Import\RowContext;
use Swark\Services\Data\ResolvedScompType;

class ResourceUsagesSheet extends ApplicationInstancesSheet implements HasPublicTitle
{
    public function publicTitle(): string
    {
        return "Resource usage";
    }

    protected ?ResourceType $databaseSchemaType;
    protected ?RelationshipType $usesRelationshipType;

    protected function beforeSheet(BeforeSheet $event)
    {
        $this->usesRelationshipType = RelationshipType::updateOrCreate(['scomp_id' => 'uses'],
            ['name' => 'Uses resource', 'source_name' => 'Consumer', 'target_name' => 'Resource']);

        $this->databaseSchemaType = ResourceType::byScompId('db-schema')->firstOrFail();
    }
    protected function importRow(RowContext $row)
    {
        $referencedResources = $row[static::RESOURCE_USAGE_SCOMP_ID_COLUMN];

        if (empty($referencedResources)) {
            return;
        }

        $this->compositeKeyContainer
            ->findScompIds($referencedResources, ['cluster', 'application_instance'], ['unique_name'])
            ->forEach(function (ResolvedScompType $resolvedScompType, array $args) use ($row) {

                $databaseSchemaTypeId = $this->databaseSchemaType->id;
                $usesRelationshipTypeId = $this->usesRelationshipType->id;
                $uniqueName = $args['unique_name'];

                $key = 'resource_' . $resolvedScompType->type . '_' . $resolvedScompType->internalId;

                if (!$this->compositeKeyContainer->idOrNull($key, $resolvedScompType->scompId)) {
                    $resource = Resource::updateOrCreate([
                        'name' => $uniqueName,
                        'resource_type_id' => $databaseSchemaTypeId,
                        'provider_type' => $resolvedScompType->type,
                        'provider_id' => $resolvedScompType->internalId,
                    ]);

                    $this->compositeKeyContainer->set($key, $uniqueName, $resource->id);
                }
                $applicationInstanceId = $this->compositeKeyContainer->get('application_instance', $row->nonEmpty(Column::SCOMP_ID_COLUMN));
                $targetResourceId = $this->compositeKeyContainer->get($key, $uniqueName);

                Relationship::updateOrCreate([
                    'source_type' => 'application_instance',
                    'source_id' => $applicationInstanceId,
                    'direction' => 'unidirectional',
                    'target_id' => $targetResourceId,
                    'target_type' => 'resource',
                    'relationship_type_id' => $usesRelationshipTypeId
                ]);
            });
    }
}

