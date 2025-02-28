<?php
declare(strict_types=1);

namespace Swark\DataModel\InformationTechnology\Domain\Repository\Component;

use Illuminate\Support\Facades\DB;
use Swark\DataModel\Kernel\Infrastructure\Repository\GroupBy;
use Swark\DataModel\Kernel\Infrastructure\Repository\GroupByTemplate;
use Swark\DataModel\Kernel\Infrastructure\Repository\MapToGroup;

class BaremetalRepository
{

    public function findGroupedBaremetals(): GroupBy
    {
        $query = <<<SQL
SELECT
b.name AS baremetal_name,
b.id AS baremetal_id,
o.id AS managed_service_provider_id,
o.name AS managed_service_provider_name,
az.id AS availability_zone_id,
az.name AS availability_zone_name,
r.id AS region_id,
r.name AS region_name,
baremetal_host.id AS host_id,
baremetal_host.name AS host_name,
sw_v.id AS software_virtualizer_id,
sw_v.name AS software_virtualizer_name,
software_virtualizer_host.id AS software_virtualizer_host_id,
software_virtualizer_host.name AS software_virtualizer_host_name,
sw_o.id AS software_os_id,
sw_o.name AS software_os_name
FROM baremetal b
LEFT JOIN managed_baremetal mb ON b.id = mb.baremetal_id
LEFT JOIN managed_account ma ON mb.managed_account_id = ma.id
LEFT JOIN availability_zone az ON mb.availability_zone_id = az.id
LEFT JOIN region r ON az.region_id = r.id
LEFT JOIN organization o ON o.id = ma.managed_service_provider_id
LEFT JOIN host baremetal_host ON b.id = baremetal_host.baremetal_id
LEFT JOIN software sw_v ON sw_v.id = (SELECT software_id FROM `release` WHERE id = baremetal_host.virtualizer_id)
LEFT JOIN software sw_o ON sw_o.id = (SELECT software_id FROM `release` WHERE id = baremetal_host.operating_system_id)
LEFT JOIN host software_virtualizer_host ON software_virtualizer_host.virtualizer_id = sw_v.id
ORDER BY managed_service_provider_name, region_name, availability_zone_name, baremetal_name
SQL;
        $rows = DB::select($query);

        $groupBy = GroupByTemplate::of('msp')
            ->nest('region')
            ->nest('az')
            ->nest('baremetal', ['baremetal', 'os', 'virtualizer', 'host'])
            ->toGroupBy();

        $mapper = new MapToGroup()
            ->property('msp', 'managed_service_provider_(.*)')
            ->property('region', 'region_(.*)')
            ->property('az', 'availability_zone_(.*)')
            ->property('baremetal', 'baremetal_(.*)')
            ->property('virtualizer', '^software_virtualizer_(.*)')
            ->property('os', 'software_os_(.*)')
            ->property('host', '^host_(.*)');

        foreach ($rows as $row) {
            $data = $mapper->map((array)$row);
            $groupBy->push($data);
        }

        return $groupBy;

    }
}
