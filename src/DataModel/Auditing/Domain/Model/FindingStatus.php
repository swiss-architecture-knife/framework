<?php

namespace Swark\DataModel\Auditing\Domain\Model;

enum FindingStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case IN_REVIEW = 'in_review';
    case DONE = 'done';

    static function toMap(): array
    {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }
}
