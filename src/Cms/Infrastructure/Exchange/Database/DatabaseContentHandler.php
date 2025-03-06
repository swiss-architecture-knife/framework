<?php

namespace Swark\Cms\Infrastructure\Exchange\Database;

use Swark\Cms\Infrastructure\Eloquent\Model\Content;
use Swark\Cms\Infrastructure\Exchange\Filesystem\LocalContent;

class DatabaseContentHandler implements ImportableDatabaseContent
{

    public function findKnownContent(string $scompPrefix): array
    {
        // load existing scomp ids for this content type
        $listOfKnownScompIds = Content::whereLike('scomp_id', $scompPrefix . '%')->pluck('updated_at', 'scomp_id')->all();

        return $listOfKnownScompIds;
    }

    public function import(LocalContent $localContent): ?Content
    {
        $content = Content::updateOrCreate([
            'scomp_id' => $localContent->scompId
        ], [
            'content' => $localContent->content,
            'type' => $localContent->targetContentType,
        ]);

        return $content;
    }
}
