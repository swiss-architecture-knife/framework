<?php

namespace Swark\Cms\Store\Suggestion;

use Swark\Cms\Store\Search\Search;

interface Suggestable
{
    /**
     * Search $search
     */
    public function suggest(Search $search): \Generator;
}
