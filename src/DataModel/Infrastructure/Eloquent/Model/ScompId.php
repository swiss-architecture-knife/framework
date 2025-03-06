<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model;

use Illuminate\Support\Str;

class ScompId
{
    private array $parts = [];

    public function __construct(array $parts)
    {
        throw_if(empty($parts), "A scomp id cannot be empty");

        foreach ($parts as $part) {
            throw_if(empty($part), "Empty scomp id path segment");
        }

        $this->parts = $parts;

    }

    public function append(string $lastPart): ScompId
    {
        return new static(array_merge($this->parts, [$lastPart]));
    }

    public function replaceLast(string $newLastPart): ScompId
    {
        $arr = $this->parts;
        $arr[sizeof($arr) - 1] = $newLastPart;
        return new static($arr);
    }

    public function __toString(): string
    {
        return Str::snake(Str::lower(implode(":", $this->parts)));
    }
}
