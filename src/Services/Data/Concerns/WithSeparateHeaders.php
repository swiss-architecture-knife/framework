<?php
namespace Swark\Services\Data\Concerns;

use Swark\Services\Data\Excel\Header;

interface WithSeparateHeaders
{
    public function header(): Header;
}
