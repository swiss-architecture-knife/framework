<?php

namespace Swark\Api\Client\Domain;

use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

readonly class NamedModelRef
{
    public function __construct(
        public Name $name,
        public Id   $id,
    )
    {

    }

    public static function of(array $item): NamedModelRef
    {
        return new static(
            name: Name::of($item['name']),
            id: Id::of($item['id']),
        );
    }
}
