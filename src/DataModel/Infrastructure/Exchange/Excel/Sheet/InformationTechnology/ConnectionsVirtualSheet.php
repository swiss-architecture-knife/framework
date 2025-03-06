<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology;

use Maatwebsite\Excel\Events\BeforeSheet;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\Relationship;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\RelationshipType;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Concerns\HasPublicTitle;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component\ApplicationInstancesSheet;
use Swark\DataModel\Infrastructure\Exchange\ResolvedScompType;

class ConnectionsVirtualSheet extends ApplicationInstancesSheet implements HasPublicTitle
{
    public function publicTitle(): string
    {
        return "Connections";
    }

    protected ?RelationshipType $connectsToRelationshipType;

    protected function beforeSheet(BeforeSheet $event)
    {
        $this->connectsToRelationshipType = RelationshipType::upsert(
            'connects_to'
            , [
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
                    'protocol_stack_id' => $protocolStackId,
                ], [
                    'port' => isset($args['port']) ? (int)$args['port'] : null,
                ]);
            });

    }
}

