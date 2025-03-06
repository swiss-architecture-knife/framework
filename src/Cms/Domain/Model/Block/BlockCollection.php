<?php

namespace Swark\Cms\Domain\Model\Block;

use Swark\Cms\Domain\Model\ResourceName;
use Swark\Cms\Domain\Model\Store\Search\Search;

class BlockCollection
{
    public function __construct(public readonly BlockResolver $blockResolver)
    {

    }

    public function resolve(string|array $search)
    {
        $searchFor = is_array($search) ? collect($search)->map(fn($item) => ResourceName::of($item))->toArray() : ResourceName::of($search);
        $searchFor = Search::of($searchFor);

        foreach ($this->blockResolver->search($searchFor) as $searchResult) {
        }
    }

    public function get(string $resourceName): Block
    {
        return $this->blockResolver->get(ResourceName::of($resourceName));
    }
}
