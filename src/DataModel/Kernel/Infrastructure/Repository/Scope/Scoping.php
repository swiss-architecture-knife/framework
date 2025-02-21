<?php
declare(strict_types=1);

namespace Swark\DataModel\Kernel\Infrastructure\Repository\Scope;

interface Scoping
{
    public function schema(): string;

    public function query(): ScopedQuery;
}
