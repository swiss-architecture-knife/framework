<?php

namespace Swark\DataModel\SoftwareArchitecture\Domain\Repository;

use Illuminate\Support\Facades\DB;
use Swark\DataModel\Kernel\Infrastructure\Repository\GroupBy;
use Swark\DataModel\Kernel\Infrastructure\Repository\GroupByTemplate;
use Swark\DataModel\Kernel\Infrastructure\Repository\MapToGroup;

class SoftwareRepository
{
    public function findSummary(): GroupBy
    {
        enable_sql_full_mode();

        $query = <<<QUERY
SELECT
    v.name AS vendor_name,
    v.id AS vendor_id,
	sw.name AS software_name,
    sw.scomp_id AS software_scomp_id,
    sw.id AS software_id,
    crit_b.id AS business_criticality_id,
    crit_b.position AS business_criticality_position,
    crit_b.name AS business_criticality_name,
    crit_i.id AS infrastructure_criticality_id,
    crit_i.position AS infrastructure_criticality_position,
    crit_i.name AS infrastructure_criticality_name,
    lz.name AS zone_name,
    IFNULL(COUNT(r.software_id), 0) AS total_releases,
    IFNULL(total_hosts.total, 0) AS total_hosts,
    IFNULL(total_runtimes.total, 0) AS total_runtimes,
    IFNULL(total_application_instances.total, 0) AS total_application_instances
FROM
	software sw
LEFT JOIN
	`release` r ON r.software_id = sw.id
LEFT JOIN
    organization v ON sw.vendor_id = v.id
LEFT JOIN
	criticality crit_i ON sw.infrastructure_criticality_id = crit_i.id
LEFT JOIN
	criticality crit_b ON sw.business_criticality_id = crit_b.id
LEFT JOIN
    (SELECT r.software_id, COUNT(*) AS total FROM host h LEFT JOIN `release` r ON r.id = h.operating_system_id GROUP BY r.software_id) as total_hosts ON total_hosts.software_id = sw.id
LEFT JOIN
    (SELECT r.software_id, COUNT(*) AS total FROM runtime ru LEFT JOIN `release` r ON r.id = ru.release_id GROUP BY r.software_id) as total_runtimes ON total_runtimes.software_id = sw.id
LEFT JOIN
    (SELECT r.software_id, COUNT(*) AS total FROM application_instance ai LEFT JOIN `release` r ON r.id = ai.release_id GROUP BY r.software_id) as total_application_instances ON total_application_instances.software_id = sw.id
LEFT JOIN
	    logical_zone lz ON lz.id = sw.logical_zone_id
GROUP BY sw.id
ORDER BY vendor_name, software_name
QUERY;
        $rows = DB::select($query);

        $groupBy = GroupByTemplate::of('vendor')
            ->nest('software', ['business_criticality', 'infrastructure_criticality'])
            ->toGroupBy();

        $mapper = new MapToGroup()
            ->property('vendor', 'vendor_(.*)')
            ->property('software', 'software_(.*)', keep: '(total|zone).*')
            ->property('business_criticality', 'business_criticality_(.*)')
            ->property('infrastructure_criticality', 'infrastructure_criticality_(.*)');

        foreach ($rows as $row) {
            $data = $mapper->map((array)$row);
            $groupBy->push($data);
        }

        return $groupBy;
    }
}
