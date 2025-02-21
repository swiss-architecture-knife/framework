<?php

namespace Swark\DataModel\Compliance\Domain\Model;

enum RelevanceType: string
{
    case NONE = 'none';
    case LOW = 'low';
    case MIDDLE = 'middle';
    case HIGH = 'high';

    static function toMap(): array
    {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }

}
