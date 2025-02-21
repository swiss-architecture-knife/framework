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
    public function search(Search $search): \Generator
    {
        // TODO Caching of queries
        $r = new ContentInDatabase();
        $t = [];

        /** @var Expression $path */
        foreach ($search as $expression) {
            switch ($expression->queryType) {
                case ExpressionType::EXACT:
                    $r = $r->where('scomp_id', $expression->resourceName->full);
                    $t[] = $expression->resourceName->full;
                    break;
                case ExpressionType::IN:
                    $databaseQuery = $expression->resourceName->full . '_';
                    $r = (sizeof($t) == 1) ? $r->whereLike('scomp_id', $databaseQuery) : $r->orWhereLike('scomp_id', $databaseQuery);
                    $t[] = $databaseQuery;
                    break;
            }
        }

        yo_debug("Loading content from database for '%s'", [implode(',', $t)]);
        $total = 0;

        /** @var ContentInDatabase $model */
        foreach ($r->get() as $model) {
            yield new Content(
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

        yo_info("Total Content returned from database: %d", [$total]);
    }
}
