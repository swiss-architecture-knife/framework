<?php
declare(strict_types=1);

namespace Swark\DataModel\Enterprise\Domain\Repository;

use Illuminate\Support\Facades\DB;

class ITArchitectureRepository
{

    public function createZoneMatrix(): array {
        $query = <<<SQL
SELECT
	lz.name,
    lz.scomp_id,
    IF(COUNT(lz_allow.id) = 0, json_array(), json_arrayagg(lz_allow.name)) AS allowed,
    IF(COUNT(lz_deny.id) = 0, json_array(), json_arrayagg(lz_deny.name)) AS denied
FROM logical_zone lz
LEFT JOIN
	relationship r_allow ON lz.id = r_allow.source_id AND r_allow.source_type = 'logical_zone' AND r_allow.relationship_type_id IN (SELECT id FROM relationship_type WHERE scomp_id = 'allow_access')
LEFT JOIN
	logical_zone lz_allow ON lz_allow.id = r_allow.target_id AND r_allow.target_type = 'logical_zone'
LEFT JOIN
	relationship r_deny ON lz.id = r_deny.source_id AND r_deny.source_type = 'logical_zone' AND r_deny.relationship_type_id IN (SELECT id FROM relationship_type WHERE scomp_id = 'deny_access')
LEFT JOIN
	logical_zone lz_deny ON lz_deny.id = r_deny.target_id AND r_deny.target_type = 'logical_zone'
GROUP BY lz.id, lz.name, lz.scomp_id
;
SQL;
        $rows = DB::select($query);
        $r = [];

        foreach ($rows as $row) {
            $row->allowed = json_decode($row->allowed);
            $row->denied = json_decode($row->denied);

            $r[] = $row;
        }

        return $r;
    }
}
