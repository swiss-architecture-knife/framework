<?php

namespace Swark\DataModel\Kernel\Infrastructure\Aspects;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HasScompId
{
    public static function bootHasScompId()
    {
        static::saving(function ($item) {
            if (in_array(HasName::class, class_uses_recursive(static::class))) {
                $suggestedScompId = $item->scomp_id ?? static::toScompId($item->name);

                if (!$item->scomp_id) {
                    // if no scomp id has been set yet, generate a scomp id from the name and the internal id. that ensures that we do not generate the same scomp id again and again
                    $query = <<<QUERY
                    SELECT auto_generated_scomp_id FROM (
                        SELECT 1 AS position, :default as auto_generated_scomp_id
                        UNION
                        SELECT 2, CONCAT(scomp_id, '-', MAX(id)) AS auto_generated_scomp_id
                            FROM `{$item->getTable()}`
                            WHERE scomp_id LIKE :name
                    ) suggestions
                    WHERE auto_generated_scomp_id IS NOT NULL ORDER BY position DESC LIMIT 1
QUERY;
                    // for simplicity, we enable full to not rewrite the query;
                    enable_sql_full_mode();

                    $item->scomp_id = DB::selectOne($query, ['default' => $suggestedScompId, 'name' => $item->name . '%'])->auto_generated_scomp_id;
                }
            }

            if ($item->scomp_id) {
                $item->scomp_id = static::toScompId($item->scomp_id);
            }
        });
    }

    public static function toScompId(string $scompifiable): string
    {
        return Str::snake(Str::lower($scompifiable));
    }

    public static function byScompId($id)
    {
        return static::where('scomp_id', $id)->firstOrFail();
    }
}
