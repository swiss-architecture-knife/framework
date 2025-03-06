<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Exchange\Ingester\Sink\Loader;

use Swark\DataModel\Infrastructure\Exchange\Ingester\Sink\Uniqueness\CompoundIdentifier;

interface ItemDataProvider
{
    public function upsertItem(CompoundIdentifier $compoundIdentifier, array $mapAttributeToRawValue): array;
}
