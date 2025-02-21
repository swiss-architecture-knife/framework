<?php

namespace Swark\Cms\Store\Search;

use Swark\Cms\Meta\Path;
use Swark\Cms\ResourceName;
use Traversable;

class Search implements \IteratorAggregate
{
    private array $searchablePaths = [];

    public static function of(ResourceName|array $someResourceNames): Search
    {
        $r = new Search();

        if (is_array($someResourceNames)) {
            foreach ($someResourceNames as $path) {
                $r->in($path);
            }
        } else {
            $r->exact($someResourceNames);
        }

        return $r;
    }

    public function in(ResourceName $resourceName)
    {
        $this->searchablePaths[] = Expression::of($resourceName, ExpressionType::IN);
        return $this;
    }

    public function exact(ResourceName $resourceName)
    {
        $this->searchablePaths[] = Expression::of($resourceName, ExpressionType::EXACT);
        return $this;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->searchablePaths);
    }
}
