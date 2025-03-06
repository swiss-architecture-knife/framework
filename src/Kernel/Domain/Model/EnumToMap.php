<?php

namespace Swark\Kernel\Domain\Model;

trait EnumToMap
{
    static function toMap(): array
    {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }
}
