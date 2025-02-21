<?php

namespace Swark\DataModel\Kernel\Infrastructure\Aspects;

use Swark\Content\Domain\Model\Displayable;

trait HasDescription
{
    public function displayDescription(): Displayable
    {
        return new Displayable($this->description);
    }
}
