<?php

namespace Swark\Frontend\UI\Components\Block;

use Illuminate\Support\Facades\View;
use Illuminate\View\Component;
use Swark\Cms\Block\Block;
use Swark\Cms\Cms;
use Swark\Cms\Content\NotFound;

/**
 * Loads content
 */
class Content extends Component
{
    public function __construct(
        public readonly ?string $id,
        public readonly bool    $editable = false,
        public readonly ?string $default = null,
    )
    {
    }

    private ?Block $block = null;

    private function getBlock(): Block
    {
        $blocks = View::shared(Cms::VIEW_KEY_BLOCKS);
        return $this->block = $blocks->get($this->id);
    }

    private function isBlockFound(): bool
    {
        return false === ($this->getBlock()->first() instanceof NotFound);
    }

    private function getRawContent(): ?string
    {
        if ($this->isBlockFound()) {
            return $this->getBlock()->render();
        }

        $missingMessages = ["No content found for key '" . $this->id . "'"];

        if ($this->default) {
            $defaultsTo = __($this->default);

            if ($defaultsTo != $this->default) {
                return $defaultsTo;
            }

            $missingMessages[] = "No translation for key '" . $this->default . "' found";
            return implode(", ", $missingMessages);
        }

        return null;
    }

    public function render()
    {
        if ($this->editable) {
            return swark_view('components.block.extended', ['path' => $this->id]);
        }

        $rawContent = $this->getRawContent();

        return swark_view('components.block.simple', [
            'block' => $this->getBlock(),
            'content' => $rawContent,
            'isFound' => $this->isBlockFound(),
        ]);
    }
}
