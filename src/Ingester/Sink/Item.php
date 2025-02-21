<?php
declare(strict_types=1);

namespace Swark\Ingester\Sink;

use Swark\Ingester\Sink\Uniqueness\CompoundIdentifier;

/**
 * A raw item represents a single item, uniquely identifiable by a key, build upon all raw data sinks (like CSV, XLSX and so on).
 */
class Item
{
    public function __construct(
        public readonly CompoundIdentifier $compoundIdentifier,
        public readonly array              $attributeToValueMapping,
    )
    {
    }
}
