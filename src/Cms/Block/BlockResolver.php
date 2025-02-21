<?php

namespace Swark\Cms\Block;

use Swark\Cms\Content\ContentSelector;
use Swark\Cms\ResourceName;
use Swark\Cms\Store\Search\Search;
use Swark\Cms\Store\Search\Searchable;
use Swark\Cms\Store\Suggestion\Suggestable;

class BlockResolver
{
    private array $mapResourceToBlock = [];

    /**
     * @param Searchable[] $searchables
     */
    public function __construct(private readonly array $searchables = [])
    {
    }

    public function search(Search $search): \Generator
    {
        foreach ($this->searchables as $searchable) {
            $block = null;

            foreach ($searchable->search($search) as $content) {
                $block = $this->ensureBlockForResource($content->resourceName)->withContent($content);

                yield $block;
            }
        }
    }

    public function get(ResourceName $resourceName)
    {
        if ($resource = $this->hasBlockForResource($resourceName)) {
            if ($resource->hasSomeContentResolved()) {
                return $resource;
            }
        }

        // assume that we did not find anything yet
        $this
            ->ensureBlockForResource($resourceName)
            ->nothingFoundYet();

        $search = Search::of($resourceName);
        foreach ($this->search($search) as $block) {
            $resourceName = $block->resourceName;
        }

        return $this->ensureBlockForResource($resourceName);
    }

    private function hasBlockForResource(ResourceName $resourceName): ?Block
    {
        return $this->mapResourceToBlock[(string)$resourceName] ?? null;
    }

    private function ensureBlockForResource(ResourceName $resource): Block
    {
        if (!isset($this->mapResourceToBlock[(string)$resource])) {
            $this->mapResourceToBlock[(string)$resource] = new Block($resource, new ContentSelector());

            foreach ($this->searchables as $searchable) {
                if ($searchable instanceof Suggestable) {
                    $singleSearch = Search::of($resource);

                    foreach ($searchable->suggest($singleSearch) as $suggestion) {
                        $this->mapResourceToBlock[(string)$resource]->addSuggestion($suggestion);
                    }
                }
            }
        }

        return $this->hasBlockForResource($resource);
    }
}
