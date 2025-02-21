<?php

namespace Swark\Services\Data\Excel;

class Header
{
    public static function numToAlpha($n)
    {
        for ($r = ""; $n >= 0; $n = intval($n / 26) - 1)
            $r = chr($n % 26 + 0x41) . $r;
        return $r;
    }

    private array $rows = [];

    public function __construct()
    {
        $this->next();
    }

    public function next(): Header
    {
        $this->rows[] = new HeaderRow();
        return $this;
    }

    public function eachRow(): \Generator
    {
        foreach ($this->rows as $row) {
            yield $row;
        }
    }

    public function first(): HeaderRow
    {
        return $this->rows[0];
    }

    public function current(): HeaderRow
    {
        return last($this->rows);
    }

    public function each(): \Generator
    {
        for ($i = 0, $m = sizeof($this->rows); $i < $m; $i++) {
            $row = $this->rows[$i];

            foreach ($row->columns() as $idx => $column) {
                yield ([$i, $row, $idx, $column]);
            }
        }
    }

    public function getMaxColumnWidth(): int
    {
        $max = 0;

        foreach ($this->each() as list($idxRow, $row, $idxColumn)) {
            $cur = $row->width();
            $max = $max > $cur ? $cur : $max;
        }

        return $max;
    }

    public function add(Column|array $columns): Header
    {
        $this->current()->add($columns);
        return $this;
    }

    public function columnKeyToIndex(): array
    {
        $r = [];

        $position = 0;

        foreach ($this->each() as list($idxRow, $header, $idxColumn, $column)) {
            if ($idxColumn == 0) {
                $position = 0;
            }

            if (!empty($column->key)) {
                $r['' . $column->key] = $position;
            }

            $position += $column->getSpan();
        }

        return $r;
    }


    public function toArray(): array
    {
        $r = [];

        foreach ($this->rows as $row) {
            $rRow = [];

            foreach ($row->columns() as $idx => $column) {
                $span = $column->getSpan();

                $col = array_fill(0, $span, '');
                $col[0] = $column->title;
                $rRow = array_merge($rRow, $col);
            }

            $r[] = $rRow;
        }

        return $r;
    }
}
