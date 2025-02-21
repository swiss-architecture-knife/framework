<?php

namespace Swark\Cms\Meta;

use Carbon\Carbon;
use Traversable;

class Changes implements \IteratorAggregate
{
    private array $changes = [];

    public function __construct(Change ...$changes)
    {
        $this->changes = $changes;
    }

    public function total(): int
    {
        return sizeof($this->changes);
    }

    public static function none(): Changes
    {
        return new static();
    }

    public static function anonymous(Carbon $createdAt): Changes
    {
        return new Changes(new Change(
            version: 1,
            author: '',
            createdAt: $createdAt,
        ));
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->changes);
    }
}

