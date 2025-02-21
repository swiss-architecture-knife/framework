<?php

namespace Swark\Cms\Content;

use Carbon\Carbon;
use Swark\Cms\Body;
use Swark\Cms\Content;
use Swark\Cms\Meta\Changes;
use Swark\Cms\Meta\Path;
use Swark\Cms\ResourceName;
use Swark\Cms\Source;
use Swark\Content\Domain\Model\ContentType;

class NotFound extends Content
{
    public function __construct()
    {
        parent::__construct(
            ResourceName::of(NotFoundResponder::PATH),
            Carbon::now(),
            Body::of('Content not available', ContentType::HTML),
            source: Source::of('not-found-generator', ''),
            changes: Changes::none(),
        );
    }
}
