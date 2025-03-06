<?php

namespace Swark\Cms\Infrastructure\Exchange\Database;

use Carbon\Carbon;
use Swark\Cms\Infrastructure\Eloquent\Model\Content;
use Swark\Cms\Infrastructure\Exchange\Filesystem\LocalContent;

interface ImportableDatabaseContent
{

    /**
     * Find all known content for the given scompid prefix
     * @param string $scompPrefix
     * @return array<string,Carbon>
     */
    public function findKnownContent(string $scompPrefix): array;

    public function import(LocalContent $localContent): ?Content;
}
