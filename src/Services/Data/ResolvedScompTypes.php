<?php

namespace Swark\Services\Data;

class ResolvedScompTypes
{
    public function __construct(public readonly array $data)
    {
    }

    public function forEach(callable $callback)
    {
        foreach ($this->data as $data) {
            /** @var \Swark\Services\Data\ResolvedScompType $resolvedScompType */
            $resolvedScompType = $data[0];
            /** @var array $args */
            $args = $data[1];
            $callback($resolvedScompType, $args);
        }
    }

    public static function of(...$args): ResolvedScompTypes
    {
        return new static(... $args);
    }
}
