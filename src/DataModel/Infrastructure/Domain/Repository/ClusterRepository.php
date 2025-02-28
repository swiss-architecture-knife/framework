<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Domain\Repository;

use Illuminate\Support\Facades\DB;

class ClusterRepository
{

    public function findClusterSummary(): array
    {
        $query = <<<SQL
SELECT
	c.id AS cluster_id,
	c.name AS cluster_name,
    SUM(case when cm.member_type = 'baremetal' then 1 else 0 end) AS cluster_total_baremetals,
    COUNT(cm.namespace_id) as cluster_total_namespaces,
    SUM(case when cm.member_type = 'runtime' then 1 else 0 end) AS cluster_total_runtimes,
    SUM(case when cm.member_type = 'application_instance' then 1 else 0 end) AS cluster_total_application_instances,
    sw.id AS target_software_id,
    sw.name AS target_software_name,
    r.id  AS target_software_release_id,
    r.version AS target_software_release_version
FROM
	cluster c
LEFT JOIN `release` r ON c.target_release_id = r.id
LEFT JOIN software sw ON r.software_id = sw.id
LEFT JOIN cluster_member cm ON c.id = cm.cluster_id
GROUP by c.id, c.name, sw.id, sw.name, r.id, r.version
ORDER BY c.name
SQL;
        $rows = DB::select($query);
        $r = $rows;

        return $r;
    }
}
