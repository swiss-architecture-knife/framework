<?php

namespace Swark\Cms\Domain\Model\Store\Search;

use Swark\Cms\Domain\Model\Content;

interface Searchable
{
    /**
     * Search $search
     * @return Content[]
     */
    public function search(Search $search): \Generator;
}
