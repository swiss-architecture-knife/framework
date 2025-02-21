<?php

namespace Swark\DataModel\Software\Domain\Event;

use Swark\DataModel\Software\Domain\Entity\Release;

readonly class BeforeReleaseSaved
{
    public function __construct(public Release $release)
    {
    }
}
