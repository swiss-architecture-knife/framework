<?php
declare(strict_types=1);

namespace Swark\DataModel\Governance\Domain\Repository;

use Illuminate\Support\Facades\DB;
use Swark\Content\Domain\Model\Displayable;
use Swark\DataModel\Governance\Domain\Entity\Kpi\Period;
use Swark\DataModel\Governance\Domain\Entity\Strategy\Strategy;

class StrategyRepository
{

    public function findKpisByStrategyAndPeriod(Strategy $strategy, Period $period): array
    {
        $q = <<<SQL
SELECT
	sub.*,
	metric.name AS metric_name,
    m.goal_value,
    m.current_value,
    m.is_goal_reached,
    k.integer_threshold_1,
    k.integer_threshold_2,
    k.percentage_threshold_1,
    k.percentage_threshold_2,
    metric.type AS metric_type,
    metric.goal_direction AS metric_goal_direction

 FROM(
	SELECT
		o.id AS measurable_id,
        'objective' AS measurable_type,
        o.name AS objective_name,
        NULL AS action_name
    FROM
		objective o
	WHERE
		o.strategy_id = ?
	UNION SELECT
		a.id AS measurable_id,
        'action' AS measurable_type,
        o.name AS objective_name,
        a.name AS action_name
    FROM
		action a
        LEFT JOIN action_assigned a_a ON a.id = a_a.action_id
        LEFT JOIN objective o ON a_a.actionable_type = 'objective' AND o.id = a_a.actionable_id
	WHERE
		o.strategy_id = ?
) sub
LEFT JOIN kpi_assigned k_a ON sub.measurable_id = k_a.measurable_id AND sub.measurable_type = k_a.measurable_type
LEFT JOIN kpi k ON k_a.kpi_id = k.id
LEFT JOIN measurement m ON m.kpi_id = k.id
LEFT JOIN metric ON k.metric_id = metric.id
WHERE m.id IS NOT NULL AND m.measurement_period_id = ?
ORDER BY objective_name, action_name, metric_name
SQL;
        $rows = DB::select($q, [$strategy->id, $strategy->id, $period->id]);
        $r = [];

        return $rows;
    }

    public function findFindingsByObjective(Strategy $strategy): array
    {
        $q = <<<SQL
SELECT o.*,
f.id AS finding_id,
f.name AS finding_name,
f.scomp_id AS finding_scomp_id,
f.description AS finding_description,
f.status AS finding_status,
-- a.begin_at AS action_begin_at,
-- a.end_at AS action_end_at,
c.position AS criticality_position,
c.name AS criticality_name,
a.id AS action_id,
a.name AS action_name,
a.description AS action_description,
a.scomp_id AS action_scomp_id,
a.status AS action_status,
a.begin_at AS action_begin_at,
a.end_at AS action_end_at
FROM objective o
LEFT JOIN finding_assigned fa ON fa.examinable_type = 'objective' AND fa.examinable_id = o.id
LEFT JOIN finding f ON fa.finding_id = f.id
LEFT JOIN criticality c ON f.criticality_id = c.id
LEFT JOIN action_assigned aa ON aa.actionable_type = 'finding' AND aa.actionable_id = f.id
LEFT JOIN action a ON aa.action_id = a.id
WHERE strategy_id  = :id
SQL;
        $rows = DB::select($q, ['id' => $strategy->id]);
        $r = [];

        foreach ($rows as $row) {
            if (!isset($r['' . $row->id])) {
                $r['' . $row->id] = (object)[...(array)$row + ['findings' => []]];
            }

            if ($row->finding_id) {
                $r['' . $row->id]->findings['' . $row->finding_id] = (object)[
                    'id' => $row->finding_id,
                    'name' => $row->finding_name,
                    'description' => Displayable::of($row->finding_description),
                    'status' => $row->finding_status,
                    'scomp_id' => $row->finding_scomp_id,
                    'criticality' => (object)[
                        'position' => $row->criticality_position,
                        'name' => $row->criticality_name,
                    ],
                    'actions' => [],
                ];
            }

            if ($row->action_id) {
                $r['' . $row->id]->findings['' . $row->finding_id]->actions[] = (object)[
                    'id' => $row->action_id,
                    'name' => $row->action_name,
                    'scomp_id' => $row->action_scomp_id,
                    'description' => Displayable::of($row->action_description),
                    'status' => $row->action_status,
                    'begin_at' => $row->action_begin_at,
                    'end_at' => $row->action_end_at,
                ];
            }
        }

        return $r;
    }
}
