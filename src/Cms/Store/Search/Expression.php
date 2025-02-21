<?php

namespace Swark\Cms\Store\Search;

use Swark\Cms\ResourceName;

class Expression
{
    public function __construct(public readonly ResourceName $resourceName, public readonly ExpressionType $queryType)
    {
    }

    public static function of(ResourceName $resourceName, ExpressionType $queryType): Expression
    {
        return new Expression($resourceName, $queryType);
    }

    public function __toString(): string
    {
        return (string)$this->resourceName . ':' . (string)$this->queryType;
    }
}
