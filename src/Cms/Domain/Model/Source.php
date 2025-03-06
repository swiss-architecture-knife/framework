<?php

namespace Swark\Cms\Domain\Model;

class Source
{
    public function __construct(public readonly string $type,
                                public readonly string $logicalPath)
    {
    }

    public static function of(string $type, string $logicalPath): Source {
        return new static($type, $logicalPath);
    }
}
