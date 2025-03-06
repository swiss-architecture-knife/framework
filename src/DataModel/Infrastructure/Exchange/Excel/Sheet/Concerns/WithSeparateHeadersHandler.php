<?php
namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Concerns;

use Maatwebsite\Excel\Writer;

class WithSeparateHeadersHandler
{
    public function __invoke(WithSeparateHeaders $exportable, Writer $writer)
    {
        // nothing to do, marker interface
    }
}
