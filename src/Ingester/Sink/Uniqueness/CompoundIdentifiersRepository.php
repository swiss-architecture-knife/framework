<?php
declare(strict_types=1);

namespace Swark\Ingester\Sink\Uniqueness;

interface CompoundIdentifiersRepository
{
    /**
     * @return CompoundIdentifier[]
     */
    public function findCompoundIdentifiers(): array;
}
