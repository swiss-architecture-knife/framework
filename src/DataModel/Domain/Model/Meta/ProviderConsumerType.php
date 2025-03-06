<?php

namespace Swark\DataModel\Domain\Model\Meta;

enum ProviderConsumerType: string
{
    case PROVIDER = 'provider';
    case CONSUMER = 'consumer';

    static function toMap(): array
    {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }

}
