<?php

namespace Swark\Cms\Store\Search;

use Swark\Cms\Content;
use Swark\Cms\Meta\Path;

interface Searchable
{
    /**
     * Search $search
     * @return Content[]
     */
    public function search(Search $search): \Generator;
}
