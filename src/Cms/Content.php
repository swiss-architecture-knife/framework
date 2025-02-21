<?php

namespace Swark\Cms;

use Carbon\Carbon;
use Swark\Cms\Meta\Changes;

class Content
{
    public function __construct(
        public readonly ResourceName $resourceName,
        public Carbon                $createdAt,
        public Body                  $body,
        public Source                $source,
        public Changes               $changes,
        public ?Carbon               $updatedAt = null,
    )
    {
    }

    public function hasChanges(): bool
    {
        return $this->changes->total() > 0;
    }
}
