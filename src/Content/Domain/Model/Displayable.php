<?php

namespace Swark\Content\Domain\Model;

use Illuminate\Support\HtmlString;
use Swark\Content\Infrastructure\Facades\Markdown;

class Displayable
{
    public function __construct(public readonly ?string $raw,
                                public readonly ?string $default = null,
                                private ?ContentType    $type = null)
    {
        if (!$type) {
            $this->type = ContentType::MARKDOWN;
        }
    }

    public function rawOrDefault(): ?string {
        if ($this->raw) {
            return $this->raw;
        }

        return $this->default ?? '???';
    }

    public function render(): string|HtmlString
    {
        return match ($this->type) {
            ContentType::HTML => new HtmlString($this->rawOrDefault()),
            ContentType::MARKDOWN => new HtmlString(Markdown::convert($this->rawOrDefault())),
            default => $this->rawOrDefault(),
        };
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public static function of(...$args): Displayable
    {
        return new static(... $args);
    }
}
