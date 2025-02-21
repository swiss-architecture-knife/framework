<?php

namespace Swark\Services\Data\Excel;

class Column
{
    public function __construct(public readonly string $title, public readonly ?string $key = null)
    {
    }

    private int $span = 1;

    private int $leftPadding = 0;

    public function getSpan(): int
    {
        return $this->span;
    }

    public function getLeftPadding(): int
    {
        return $this->leftPadding;
    }

    public function leftPadding(int $leftPadding): Column
    {
        $this->leftPadding = $leftPadding;
        return $this;
    }

    public function span(int $span): Column
    {
        $this->span = $span;
        return $this;
    }

    public static function of(string $title, ?string $key = null, int $span = 1, int $leftPadding = 0)
    {
        return (new static($title, $key))->span($span)->leftPadding($leftPadding);
    }


    public static function empty(int $span = 1): Column
    {
        return static::of('', key: null, span: $span);
    }

    const SCOMP_ID_COLUMN = 'scomp_id';

    public static function scompId(?string $key = null): Column
    {
        $key = $key ?? static::SCOMP_ID_COLUMN;

        return static::of('Scomp-ID', $key);
    }
}
