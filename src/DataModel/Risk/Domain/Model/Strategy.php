<?php

namespace Swark\DataModel\Risk\Domain\Model;

enum Strategy: string
{
    case ACCEPT = 'accept';
    case FIX = 'fix';

    static function toMap(): array
    {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }
}
