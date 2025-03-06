<?php
namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Concerns;

use Swark\DataModel\Infrastructure\Exchange\Excel\Header;

interface WithSeparateHeaders
{
    public function header(): Header;
}
