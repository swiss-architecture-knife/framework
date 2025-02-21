<?php

namespace Swark\DataModel\Infrastructure\Domain\Model;

enum ClusterMode: string
{
    case FAILOVER = 'failover';
    case REPLICA = 'replica';
    case LOADBALANCING = 'lb';

    static function toMap(): array
    {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }

}
