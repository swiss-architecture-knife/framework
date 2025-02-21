<?php

namespace Swark\Api\Client\Types;

readonly class Description
{
    public function __construct(public string $value)
    {
    }

    public static function of(string $value): Description
    {
        return new static($value);
    }
}
