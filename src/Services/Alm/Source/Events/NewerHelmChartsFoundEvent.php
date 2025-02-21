<?php

namespace Swark\Services\Alm\Source\Events;

use Dreitier\Alm\Helm\Chart\Release;

class NewerHelmChartsFoundEvent
{
    public function __construct(public readonly Release $helmChartRelease,
                                public readonly array   $availableVersion,
    )
    {
    }
}
