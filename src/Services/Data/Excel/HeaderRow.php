<?php

namespace Swark\Services\Data\Excel;

class HeaderRow
{
    private array $columns = [];

    public function add(Column|array $columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $this->columns = array_merge($this->columns, $columns);

        return $this;
    }

    public function columns(): array
    {
        return $this->columns;
    }

    public function width(): int
    {
        $r = 0;

        foreach ($this->columns as $column) {
            $r += $column->getSpan();
        }

        return $r;
    }
}
