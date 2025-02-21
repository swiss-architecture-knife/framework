<?php

namespace Swark\Api\Client\Types;

readonly class NamedId
{
    public function __construct(public string $value,
                                public string $type = 'scomp')
    {
    }

    public static function of(string $value, string $type = 'scomp'): NamedId
    {
        return new static($value, $type);
    }

    public function __toString(): string
    {
        return $this->type . ":" . $this->value;
    }
}
