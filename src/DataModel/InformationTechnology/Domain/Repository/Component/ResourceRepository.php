<?php
declare(strict_types=1);

namespace Swark\DataModel\InformationTechnology\Domain\Repository\Component;

use Illuminate\Support\Facades\DB;
use Swark\DataModel\Kernel\Infrastructure\Repository\GroupBy;
use Swark\DataModel\Kernel\Infrastructure\Repository\GroupByTemplate;
use Swark\DataModel\Kernel\Infrastructure\Repository\MapToGroup;

class ResourceRepository
{

    public function findGroupedResourcesOfType(int $resourceTypeId): GroupBy
    {
        $query = <<<SQL
SELECT
    ci_provider.ref_id AS provider_id,
    ci_provider.name AS provider_name,
    CASE
		WHEN ai_stage.id IS NOT NULL
			THEN ai_stage.name
        WHEN c_stage.id IS NOT NULL
			THEN c_stage.name
	END AS provider_stage,
	r.name AS resource_name,
    r.id AS resource_id,
    ci_consumer.ref_type AS consumer_type,
    ci_consumer.name AS consumer_name,
    ci_consumer.ref_id AS consumer_id
FROM
	resource r
LEFT JOIN
	configuration_item ci_provider ON r.provider_id = ci_provider.ref_id AND r.provider_type = ci_provider.ref_type
LEFT JOIN
	application_instance ai ON ai.id = r.provider_id AND r.provider_type = 'application_instance'
LEFT JOIN
	stage ai_stage ON ai.stage_id  = ai_stage.id
LEFT JOIN
	cluster c ON c.id = r.provider_id AND r.provider_type = 'cluster'
LEFT JOIN
	stage c_stage ON c.stage_id = c_stage.id
LEFT JOIN
	managed_subscription ms ON ms.id = r.provider_id AND r.provider_type = 'subscription'
LEFT JOIN
	relationship rel ON rel.target_type = 'resource' AND rel.target_id = r.id
LEFT JOIN
	configuration_item ci_consumer ON rel.source_type = ci_consumer.ref_type AND rel.source_id = ci_consumer.ref_id
WHERE
	r.resource_type_id = :resource_type_id
ORDER BY ci_provider.name, resource_name;
SQL;
        $rows = DB::select($query, ['resource_type_id' => $resourceTypeId]);

        $groupBy = GroupByTemplate::of('provider')
            ->nest('resource')
            ->nest('consumer', [])
            ->toGroupBy();

        $mapper = new MapToGroup()
            ->property('provider', 'provider_(.*)')
            ->property('resource', 'resource_(.*)')
            ->property('consumer', 'consumer_(.*)');

        foreach ($rows as $row) {
            $data = $mapper->map((array)$row);
            $groupBy->push($data);
        }

        return $groupBy;
    }
}
