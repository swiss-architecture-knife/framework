<?php
declare(strict_types=1);

namespace Swark\Ingester\Sink\Structure;

use Illuminate\Support\Str;
use Swark\Ingester\Model\Relationship\Attribute;

/**
 * Maps a raw (CSV, Excel, ...) column to a model attribute
 */
class Column
{
    public function __construct(
        public readonly string    $name,
        public readonly Attribute $attribute,
    )
    {
    }

    public static function toDefaultAttributeName(string $s): string
    {
        return Str::replace('-', '_', Str::snake($s));
    }

}
