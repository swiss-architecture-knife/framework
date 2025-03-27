<?php

namespace Swark\Kernel\Infrastructure\Database\Dialect;

class SqliteDialect extends AbstractSqlDialect
{
    protected function jsonGroupArray($args): string
    {
        return 'json_group_array(' . $args . ')';
    }
}
