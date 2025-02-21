<?php
declare(strict_types=1);

namespace Swark\Frontend\UI\Components\Diagram;

/**
 * Convert a textual diagram representation to its output format
 */
class Transformer
{
    public function __construct(public readonly \Closure $transformer)
    {
    }

    public function transform(Source $diagramSource): Output
    {
        return $this->transformer->call($this, $diagramSource);
    }
}
