<?php

namespace Swark\Cms;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Swark\Content\Domain\Factory\MarkdownFactory;
use Swark\Content\Domain\Model\ContentType;
use Swark\Content\Domain\Model\WithContentType;
use Swark\Content\Infrastructure\Facades\Markdown;

// TODO Refactor to remove duplicates with Displayable class
class Body implements WithContentType
{
    public function __construct(
        private readonly string     $raw,
        public readonly ContentType $contentType)
    {
    }

    public function raw(): string
    {
        return $this->raw;
    }

    public function render(): string
    {
        return match ($this->contentType) {
            ContentType::HTML => new HtmlString($this->raw()),
            ContentType::BLADE => Blade::render($this->raw(), []),
            ContentType::MARKDOWN => new HtmlString(Markdown::convert(MarkdownFactory::create($this->raw())->content)),
        };
    }

    public static function of(string $raw, ContentType $contentType)
    {
        return new static($raw, $contentType);
    }

    public function contentType(): ContentType
    {
        return $this->contentType;
    }
}
