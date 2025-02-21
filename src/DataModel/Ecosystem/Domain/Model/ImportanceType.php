<?php

namespace Swark\DataModel\Ecosystem\Domain\Model;

enum ImportanceType: string
{
    case NORMAL = 'normal';
    case HIGH = 'high';
    case VERY_HIGH = 'very_high';

    static function toMap(): array
    {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }

}
