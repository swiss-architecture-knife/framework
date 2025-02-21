<?php

namespace Swark\Cms\Meta;

use Carbon\Carbon;

class Change
{
    public function __construct(
        public readonly string $version,
        public readonly string $author,
        public readonly ?Carbon $createdAt,
    )
    {
    }
}
