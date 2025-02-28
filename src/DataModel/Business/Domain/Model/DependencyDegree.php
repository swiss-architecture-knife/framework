<?php

namespace Swark\DataModel\Business\Domain\Model;

enum DependencyDegree: string
{
    case NORMAL = 'normal';
    case HIGH = 'high';
    case VERY_HIGH = 'very_high';

    static function toMap(): array
    {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }

}
