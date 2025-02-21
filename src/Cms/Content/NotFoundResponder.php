<?php

namespace Swark\Cms\Content;

use Swark\Cms\Store;
use Swark\Cms\Store\Search\Expression;
use Swark\Cms\Store\Search\Search;

class NotFoundResponder implements Store\Search\Searchable
{
    const PATH = '404';

    public function search(Search $search): \Generator
    {
        /** @var Expression $expression */
        foreach ($search as $expression) {
            if ($expression->resourceName->full == static::PATH) {
                yield new NotFound();
            }
        }
    }
}
