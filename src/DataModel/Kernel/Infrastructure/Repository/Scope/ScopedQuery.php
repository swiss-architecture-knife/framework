<?php
declare(strict_types=1);

namespace Swark\DataModel\Kernel\Infrastructure\Repository\Scope;

use Illuminate\Database\Eloquent\Builder;

class ScopedQuery
{
    public function __construct(public readonly string  $modelType,
                                public readonly Builder $builder)
    {
    }

    public static function of(string $modelType, Builder $builder)
    {
        return new static($modelType, $builder);
    }
}
