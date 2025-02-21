<?php
declare(strict_types=1);

namespace Swark\Ingester\Sink\Loader;

use Swark\Ingester\Sink\Uniqueness\CompoundIdentifier;

interface ItemDataProvider
{
    public function upsertItem(CompoundIdentifier $compoundIdentifier, array $mapAttributeToRawValue): array;
}
