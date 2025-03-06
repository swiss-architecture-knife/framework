<?php

namespace Swark\DataModel\Domain\Event\SoftwareArchitecture;

use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Release;

readonly class BeforeReleaseSaved
{
    public function __construct(public Release $release)
    {
    }
}
