<?php

namespace Swark\Cms\Store;

use Swark\Cms\Body;
use Swark\Cms\Content;
use Swark\Cms\Meta\Changes;
use Swark\Cms\ResourceName;
use Swark\Cms\Source;
use Swark\Cms\Store\Search\Expression;
use Swark\Cms\Store\Search\ExpressionType;
use Swark\Cms\Store\Search\Search;
use Swark\DataModel\Content\Domain\Entity\Content as ContentInDatabase;

class DatabaseStore implements \Swark\Cms\Store\Search\Searchable
{
    private array $databaseCache = [];

    /**
     * Load content from either cache or if the cache misses, load from database
     *
     * @param Search $search
     * @return array|mixed
     */
    private function loadFromCacheOrDatabase(Search $search) {
        $contentInDatabaseModelQuery = new ContentInDatabase();
        $searchedResources = [];

        /** @var Expression $path */
        foreach ($search as $expression) {
            switch ($expression->queryType) {
                case ExpressionType::EXACT:
                    $contentInDatabaseModelQuery = $contentInDatabaseModelQuery->where('scomp_id', $expression->resourceName->full);
                    $searchedResources[] = $expression->resourceName->full;
                    break;
                case ExpressionType::IN:
                    $searchForResource = $expression->resourceName->full . '_';
                    $contentInDatabaseModelQuery = (sizeof($searchedResources) == 1) ? $contentInDatabaseModelQuery->whereLike('scomp_id', $searchForResource) : $contentInDatabaseModelQuery->orWhereLike('scomp_id', $searchForResource);
                    $searchedResources[] = $searchForResource;
                    break;
            }
        }

        $uniqueSearchKey = implode(',', $searchedResources);

        if (!isset($this->databaseCache[$uniqueSearchKey])) {
            $items= [];

            yo_debug("Loading content from database for '%s'", [$uniqueSearchKey]);
            $total = 0;

            /** @var ContentInDatabase $model */
            foreach ($contentInDatabaseModelQuery->get() as $model) {
                $items[] = new Content(
                    resourceName: ResourceName::of($model->scomp_id),
                    createdAt: $model->created_at,
                    body: Body::of($model->content, $model->contentType()),
                    source: Source::of('database', $model->scomp_id),
                    changes: Changes::none(),
                    // TODO
                    updatedAt: $model->updated_at,
                );

                $total++;
            }

            yo_info("Total content returned from database: %d", [$total]);

            $this->databaseCache[$uniqueSearchKey] = $items;
        }

        return $this->databaseCache[$uniqueSearchKey];
    }

    public function search(Search $search): \Generator
    {
        /** @var Content $content */
        foreach ($this->loadFromCacheOrDatabase($search) as $content) {
            yield $content;
        }
    }
}
