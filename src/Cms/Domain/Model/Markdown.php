<?php

namespace Swark\Cms\Domain\Model;

readonly class Markdown
{
    public function __construct(public string $content,
                                public int    $contentPosition = 0,
                                public ?array $frontmatter = null)
    {
    }
}
