<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Exchange\Ingester\Sink\Uniqueness;

interface CompoundIdentifiersRepository
{
    /**
     * @return CompoundIdentifier[]
     */
    public function findCompoundIdentifiers(): array;
}
