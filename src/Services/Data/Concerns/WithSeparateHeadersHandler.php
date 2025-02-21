<?php
namespace Swark\Services\Data\Concerns;

use Maatwebsite\Excel\Writer;

class WithSeparateHeadersHandler
{
    public function __invoke(WithSeparateHeaders $exportable, Writer $writer)
    {
        // nothing to do, marker interface
    }
}
