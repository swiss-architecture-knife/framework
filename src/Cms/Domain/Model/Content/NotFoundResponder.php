<?php

namespace Swark\Cms\Domain\Model\Content;

use Swark\Cms\Domain\Model\Store\Search\Expression;
use Swark\Cms\Domain\Model\Store\Search\Search;

class NotFoundResponder implements \Swark\Cms\Domain\Model\Store\Search\Searchable
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
