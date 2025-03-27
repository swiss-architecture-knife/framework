<?php

namespace Swark\Kernel\Infrastructure\Database\Dialect;

abstract class AbstractSqlDialect
{
    /**
     * Parse the given SQL code and replaces each occurrence of `{{::$method $params}}` with references to this class' method.
     *
     * @param string $sql
     * @return string
     * @throws \Throwable
     */
    public function parse(string $sql): string
    {
        // e.g.:
        //  SELECT {{::jsonGroupArray my_table.col}} FROM bla GROUP BY my_table.col
        preg_match_all('/{{\s*::(?<method>\w+)\s*(?<args>[^\}\}]*)\}\}/', $sql, $matches);

        if ($matches) {
            foreach ($matches[0] as $idx => $fullString) {
                $method = $matches['method'][$idx];
                $args = $matches['args'][$idx];

                throw_if($method == 'parse', "Cannot call parse()");
                throw_if(!method_exists($this, $method), "Method $method is not available and can not be abstracted");
                $sql = str_replace($fullString, $this->{$method}($args), $sql);
            }
        }

        return $sql;
    }

    /**
     * Enable full group mode; specific for MySQL.
     *
     * @return void
     */
    public function enableLazyGroupByMode(): void
    {

    }

    /**
     * Inject function to create JSON array for groupings
     *
     * @param $args
     * @return string
     */
    abstract protected function jsonGroupArray($args): string;
}
