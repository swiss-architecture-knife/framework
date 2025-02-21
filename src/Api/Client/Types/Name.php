<?php

namespace Swark\Api\Client\Types;

readonly class Name
{
    public function __construct(public string $value)
    {
    }

    public static function of(string $value): Name
    {
        return new static($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
