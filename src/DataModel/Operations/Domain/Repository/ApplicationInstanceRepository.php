<?php
declare(strict_types=1);

namespace Swark\DataModel\Operations\Domain\Repository;

use Illuminate\Support\Facades\DB;
use Swark\DataModel\Kernel\Infrastructure\Repository\GroupBy;
use Swark\DataModel\Kernel\Infrastructure\Repository\GroupByTemplate;
use Swark\DataModel\Kernel\Infrastructure\Repository\MapToGroup;

class ApplicationInstanceRepository
{

    public function findApplicationInstances(int $id): GroupBy
    {
        $sql = <<<SQL
SELECT
	n.name AS namespace_name,
    n.id AS namespace_id,
    cm.id AS cluster_member_id,
    ai.scomp_id AS application_instance_scomp_id,
    sw.name AS software_name,
    sw.id AS software_id,
    r.version AS release_version,
    r.id AS release_id,
    s.id AS stage_id,
    s.name AS stage_name,
    h.name AS host_name,
    h.id AS host_id,
    rt.name AS runtime_name,
    rt.id AS runtime_id
FROM cluster_member cm
LEFT JOIN namespace n ON n.id = cm.namespace_id
LEFT JOIN application_instance ai ON ai.id = cm.member_id
LEFT JOIN `release` r ON ai.release_id = r.id
LEFT JOIN software sw ON r.software_id = sw.id
LEFT JOIN stage s ON s.id = ai.stage_id
LEFT JOIN host h ON ai.executor_id = h.id AND ai.executor_type = 'host'
LEFT JOIN runtime rt ON ai.executor_id = rt.id AND ai.executor_type = 'runtime'
WHERE
	cm.cluster_id = ?
    AND
    cm.member_type = 'application_instance'
ORDER BY n.name;
SQL;
        $rows = DB::select($sql, [$id]);
        $groupBy = GroupByTemplate::of('namespace')
            ->nest('cluster_member', ['software','release', 'stage', 'host', 'runtime'], hideGroup: true)
            ->toGroupBy();

        $mapper = new MapToGroup()
            ->property('namespace', 'namespace_(.*)')
            ->property('cluster_member', 'cluster_member_(.*)')
            ->property('software', 'software_(.*)')
            ->property('release', 'release_(.*)')
            ->property('stage', 'stage_(.*)')
            ->property('host', 'host_(.*)')
            ->property('runtime', 'runtime_(.*)');

        foreach ($rows as $row) {
            $data = $mapper->map((array)$row);
            $groupBy->push($data);
        }

        return $groupBy;
    }
}
