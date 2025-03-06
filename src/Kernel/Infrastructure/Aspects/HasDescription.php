<?php

namespace Swark\Kernel\Infrastructure\Aspects;

use Swark\Kernel\Domain\Model\Displayable;

trait HasDescription
{
    public function displayDescription(): Displayable
    {
        return new Displayable($this->description);
    }
}
