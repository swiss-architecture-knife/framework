<?php

namespace Swark\DataModel\Auditing\Domain\Model;

enum TreatmentStrategy: string
{
    case ACCEPT = 'accept';
    case FIX = 'fix';

    static function toMap(): array
    {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }
}
