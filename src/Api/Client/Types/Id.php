<?php

namespace Swark\Api\Client\Types;

readonly class Id
{
    public function __construct(public int|string $value)
    {
    }

    public static function of(int|string $value): Id
    {
        return new static($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
