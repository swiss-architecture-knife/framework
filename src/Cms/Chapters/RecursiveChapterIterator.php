<?php

namespace Swark\Cms\Chapters;

use RecursiveIterator;

class RecursiveChapterIterator implements RecursiveIterator
{

    private int $index = 0;

    public function __construct(public readonly array $tocItems)
    {

    }

    public function current(): mixed
    {
        return $this->tocItems[$this->index] ?? null;
    }

    public function next(): void
    {
        $this->index++;
    }

    public function key(): mixed
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return $this->index >= 0 && $this->index < sizeof($this->tocItems);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function hasChildren(): bool
    {
        return $this->current()->hasChildren();
    }

    public function getChildren(): ?RecursiveIterator
    {
        return new RecursiveChapterIterator($this->current()->itemsRaw());
    }
}
