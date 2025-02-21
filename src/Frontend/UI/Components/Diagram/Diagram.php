<?php
declare(strict_types=1);

namespace Swark\Frontend\UI\Components\Diagram;

use Illuminate\Support\Arr;

class Diagram
{
    public function __construct(public readonly string $id,
                                public readonly array  $sources,
    )
    {
    }

    public function first(): ?Source
    {
        return Arr::first($this->sources);
    }

    public function firstOutput(): ?Output
    {
        return $this->first()?->output();
    }

    public function firstError(): string|null
    {
        if ($first = $this->firstOutput()) {
            return $first->error;
        }

        return null;
    }

    public function sources(): array
    {
        return $this->sources;
    }
}
