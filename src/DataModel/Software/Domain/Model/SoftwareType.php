<?php
namespace Swark\DataModel\Software\Domain\Model;

enum SoftwareType: string {
    case APPLICATION = 'application';
    case LIBRARY = 'library';
    case DOCUMENTATION = 'documentation';
    case FRAMEWORK = 'framework';
    case LANGUAGE = 'language';

    static function toMap(): array {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }
}
