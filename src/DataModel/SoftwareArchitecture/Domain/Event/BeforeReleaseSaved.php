<?php

namespace Swark\DataModel\SoftwareArchitecture\Domain\Event;

use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Release;

readonly class BeforeReleaseSaved
{
    public function __construct(public Release $release)
    {
    }
}
