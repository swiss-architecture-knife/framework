<?php

namespace Swark\Kernel\Infrastructure\Database\Dialect;

use Illuminate\Support\Facades\DB;

class MysqlDialect extends AbstractSqlDialect
{
    public function enableLazyGroupByMode(): void {
        /** Sets sql_mode=only_full_group_by */
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
    }


    protected function jsonGroupArray($args): string
    {
        return 'json_arrayagg(' . $args . ')';
    }
}
