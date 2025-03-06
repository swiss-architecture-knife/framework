<?php

namespace Swark\Cms\Domain\Model\Store\Suggestion;

use Swark\Cms\Domain\Model\Store\Search\Search;

interface Suggestable
{
    /**
     * Search $search
     */
    public function suggest(Search $search): \Generator;
}
