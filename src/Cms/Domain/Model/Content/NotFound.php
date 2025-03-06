<?php

namespace Swark\Cms\Domain\Model\Content;

use Carbon\Carbon;
use Swark\Cms\Domain\Model\Body;
use Swark\Cms\Domain\Model\Content;
use Swark\Cms\Domain\Model\ContentType;
use Swark\Cms\Domain\Model\Meta\Changes;
use Swark\Cms\Domain\Model\ResourceName;
use Swark\Cms\Domain\Model\Source;

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
