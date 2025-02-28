<?php

namespace Swark\DataModel\Governance\Domain\Model;

enum TechnologyType: string
{
    case PROTOCOL = 'protocol';
    case LANGUAGE = 'language';
    case FRAMEWORK = 'framework';
    case DATA_FORMAT = 'data-format';
    case CONCEPT = 'concept';
    case OTHER = 'other';

    static function toMap(): array
    {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }

}
