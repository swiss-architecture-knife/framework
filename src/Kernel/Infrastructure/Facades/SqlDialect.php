<?php
namespace Swark\Kernel\Infrastructure\Facades;

/**
 * @method static string parse(string $sql)
 * @method static void enableLazyGroupByMode()
 */
class SqlDialect extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string {
        return 'sql_dialect';
    }
}
