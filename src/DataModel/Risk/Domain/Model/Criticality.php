<?php
namespace Swark\DataModel\Risk\Domain\Model;

enum Criticality: string {
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    static function toMap(): array {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }
}
