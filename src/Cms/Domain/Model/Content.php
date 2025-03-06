<?php

namespace Swark\Cms\Domain\Model;

use Carbon\Carbon;
use Swark\Cms\Domain\Model\Meta\Changes;

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
