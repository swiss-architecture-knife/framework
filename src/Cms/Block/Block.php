<?php

namespace Swark\Cms\Block;

use Swark\Cms\Content;
use Swark\Cms\Content\ContentSelector;
use Swark\Cms\ResourceName;
use Swark\Cms\Store\Suggestion\Suggestion;
use Traversable;

class Block implements \IteratorAggregate
{
    private BlockStatus $blockStatus;
    private array $contents = [];
    private array $suggestions = [];

    public function __construct(
        public readonly ResourceName    $resourceName,
        public readonly ContentSelector $contentSelector,
    )
    {
        $this->blockStatus = BlockStatus::UNINITALIZED;
    }

    public function nothingFoundYet(): Block
    {
        if ($this->blockStatus !== BlockStatus::RESOLVED) {
            $this->blockStatus = BlockStatus::MISSING;
        }

        return $this;
    }

    public function hasSomeContentResolved(): bool
    {
        return $this->blockStatus == BlockStatus::RESOLVED;
    }

    public function setContentResolved(): Block
    {
        $this->blockStatus = BlockStatus::RESOLVED;

        return $this;
    }

    public function withContent(Content $content): Block
    {
        $this->contents[] = $content;
        $this->setContentResolved();

        return $this;
    }

    public function addSuggestion(Suggestion $suggestion): Block
    {
        $this->suggestions[] = $suggestion;
        return $this;
    }

    public function suggestions(): array
    {
        return $this->suggestions;
    }

    public function hasSuggestions(): bool
    {
        return sizeof($this->suggestions) > 0;
    }

    public function first(): Content
    {
        return $this->contentSelector->select($this->contents);
    }

    public function render(): string
    {
        return $this->first()->body->render();
    }

    public function id(): string
    {
        return md5($this->resourceName->full);
    }

    public function unique(?string $prefix = null, ?string $suffix = null): string
    {
        return ($prefix ?? '') . $this->id() . ($suffix ?? '');
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->contents);
    }
}
