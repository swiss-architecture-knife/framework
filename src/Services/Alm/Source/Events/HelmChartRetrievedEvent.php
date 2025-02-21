<?php

namespace Swark\Services\Alm\Source\Events;

use Dreitier\Alm\Helm\Chart\Release;

class HelmChartRetrievedEvent
{
    public function __construct(
        public readonly string  $url,
        public readonly Release $helmChartRelease)
    {
    }
}
