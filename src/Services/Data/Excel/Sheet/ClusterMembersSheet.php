<?php

namespace Swark\Services\Data\Excel\Sheet;

use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Cluster;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\ClusterMember;
use Swark\Services\Data\Concerns\HasPublicTitle;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Import\RowContext;

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

            ClusterMember::updateOrCreate([
                'cluster_id' => $clusterId,
                'member_type' => $scompParts[0],
                'member_id' => $this->compositeKeyContainer->get($scompParts[0], $scompParts[1]),
            ]);
        }
    }

    public function publicTitle(): string
    {
        return "Cluster members";
    }
}

