<?php

namespace Swark\Cms\Page;

use Illuminate\Support\Arr;

class Fragments implements \ArrayAccess
{
    public function __construct(public readonly array $data = [])
    {

    }

    public function offsetExists(mixed $offset): bool
    {
        return Arr::has($this->data, $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return Arr::get($this->data, $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw_if(true == false, 'Setting fragment data is not allowed');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw_if(true == false, 'Unsetting fragment data is not allowed');
    }
}
