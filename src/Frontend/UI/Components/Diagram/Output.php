<?php
declare(strict_types=1);

namespace Swark\Frontend\UI\Components\Diagram;

class Output
{
    public function __construct(
        public readonly string  $content,
        public readonly string  $url,
        public readonly ?string $error = null)
    {
    }
}
