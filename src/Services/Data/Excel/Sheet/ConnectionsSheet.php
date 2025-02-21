<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Events\BeforeSheet;
use Swark\DataModel\Ecosystem\Domain\Entity\Relationship;
use Swark\DataModel\Ecosystem\Domain\Entity\RelationshipType;
use Swark\Services\Data\Concerns\HasPublicTitle;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Import\RowContext;
use Swark\Services\Data\ResolvedScompType;

class ConnectionsSheet extends ApplicationInstancesSheet implements HasPublicTitle
{
    public function publicTitle(): string
    {
        return "Connections";
    }

    protected ?RelationshipType $connectsToRelationshipType;

    protected function beforeSheet(BeforeSheet $event)
    {
        $this->connectsToRelationshipType = RelationshipType::updateOrCreate([
            'scomp_id' => 'connects_to'
        ], [
            'name' => 'Connects to', 'source_name' => 'Source', 'target_name' => 'Target'
        ]);
    }
    protected function importRow(RowContext $row)
    {
        $referencedConfigurationItem = $row[static::CONNECTS_TO_SCOMP_ID_COLUMN];

        if (empty($referencedConfigurationItem)) {
            return;
        }

        $this
            ->compositeKeyContainer
            ->findScompIds($referencedConfigurationItem, ['cluster', 'application_instance', 'resource'], ['protocol_stack_scomp_id', 'port'])
            ->forEach(function (ResolvedScompType $resolvedScompType, array $args) use ($row) {
                $applicationInstanceId = $this
                    ->compositeKeyContainer
                    ->get('application_instance', $row->nonEmpty(Column::SCOMP_ID_COLUMN));

                $protocolStackId = $this
                    ->compositeKeyContainer
                    ->idOrNull('protocol_stack', $args['protocol_stack_scomp_id'] ?? null);

                Relationship::updateOrCreate([
                    'source_type' => 'application_instance',
                    'source_id' => $applicationInstanceId,
                    'direction' => 'unidirectional',
                    'target_id' => $resolvedScompType->internalId,
                    'target_type' => $resolvedScompType->type,
                    'relationship_type_id' => $this->connectsToRelationshipType->id,
                ], [
                    'port' => isset($args['port']) ? (int)$args['port'] : null,
                    'protocol_stack_id' => $protocolStackId,
                ]);
            });

    }
}

