<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component;

use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\ClusterMember;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Concerns\HasPublicTitle;

/**
 * Cluster members is a second pass over "Clusters" sheet
 */
class ClusterMembersSheet extends ClustersSheet implements HasPublicTitle
{
    protected function importRow(RowContext $row)
    {
        $clusterId = $this->compositeKeyContainer->get('cluster', Cluster::toScompId($row->nonEmpty(Column::SCOMP_ID_COLUMN)));
        $members = $row[static::MEMBERS_SCOMP_ID_COLUMN];

        // only deal with that row if members are specified for this cluster
        if (empty($members)) {
            return;
        }

        $data = explode(",", $members);

        foreach ($data as $item) {
            $scompParts = explode(":", $item);
            $allowedMemberTypes = [
                /* alias => member type */
                'host' => 'host',
                'parent-host' => 'host',
                'child-host' => 'host',
                'runtime' => 'runtime',
                'rt' => 'runtime',
                'application_instance' => 'application_instance',
                'ai' => 'application_instance',
            ];

            throw_if(!isset($allowedMemberTypes[$scompParts[0]]), "Member type {$scompParts[0]} is not allowed. You can only use [" . implode(", ", $allowedMemberTypes) . '] as member types');
            $memberType = $allowedMemberTypes[$scompParts[0]];

            ClusterMember::updateOrCreate([
                'cluster_id' => $clusterId,
                'member_type' => $memberType,
                'member_id' => $this->compositeKeyContainer->get($memberType, $scompParts[1]),
            ]);
        }
    }

    public function publicTitle(): string
    {
        return "Cluster members";
    }
}

