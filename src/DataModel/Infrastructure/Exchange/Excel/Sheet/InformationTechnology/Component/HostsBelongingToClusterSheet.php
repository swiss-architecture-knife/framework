<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\BeforeSheet;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\ClusterMember;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Host;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Concerns\HasPublicTitle;

class HostsBelongingToClusterSheet extends HostsSheet implements HasPublicTitle
{
    public function publicTitle(): string
    {
        return "Hosts belonging to a cluster";
    }

    protected function importRow(RowContext $row)
    {
        $parent = $row->explode(":", static::PARENT_SCOMP_ID_COLUMN);

        if ($parent[0] != 'cluster') {
            return;
        }

        $clusterId = $this->compositeKeyContainer->get('cluster', $parent[1]);

        $memberType = 'host';

        ClusterMember::updateOrCreate([
            'cluster_id' => $clusterId,
            'member_type' => 'host',
            'member_id' => $this->compositeKeyContainer->get($memberType, $row[Column::SCOMP_ID_COLUMN]),
        ]);
    }
}

