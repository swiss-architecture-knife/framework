<?php

namespace Swark\DataModel\Software\Domain\Model;

enum UsageType: string
{
    case CONSOLE = 'console';
    case WEBAPP = 'webapp';
    case CLIENT = 'client';
    case SERVER = 'server';
    case MOBILE = 'mobile';

    static function toMap(): array {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }

}
