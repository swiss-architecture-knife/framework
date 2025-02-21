<?php

namespace Swark\Frontend\UI\Components\Chapter;

use Illuminate\Support\Facades\View;
use Illuminate\View\Component;
use Swark\Cms\Chapters\Item;
use Swark\Cms\Cms;
use Swark\Cms\Page\Configuration;
use TorMorten\Eventy\Facades\Eventy;

/**
 * Creates a label for a chapter
 */
class Label extends Component
{
    public function __construct(
        public readonly ?Item   $chapter,
        public readonly ?bool   $enableNumbering = null,
        public readonly ?string $numberPrefix = null,
        public readonly ?string $labelPrefix = null,
        public readonly ?string $numberConcat = null,
        public readonly ?string $numberSuffix = null,
        public readonly ?string $labelSuffix = null,
        public readonly ?string $context = null,
    )
    {
    }

    public function render()
    {
        /** @var Configuration $pageConfig */
        $pageConfig = View::shared(Cms::VIEW_KEY_PAGE_CONFIG);

        $withNumbering = $this->enableNumbering ?? $pageConfig->hasLabelNumberingEnabled();
        $numberPrefix = $this->numberPrefix ?? $pageConfig->getNumberPrefix();
        $numberConcat = $this->numberConcat ?? $pageConfig->getNumberConcat();
        $numberSuffix = $this->numberSuffix ?? $pageConfig->getNumberSuffix();
        $labelPrefix = $this->labelPrefix ?? $pageConfig->getLabelPrefix();
        $labelSuffix = $this->labelSuffix ?? $pageConfig->getLabelSuffix();

        $number = '';


        if ($withNumbering && $this->chapter) {
            $fullPathItems = $this->chapter->walkToRoot(fn(Item $item) => $item->position);
            $path = implode($numberConcat, $fullPathItems);
            $number = $numberPrefix . $path .$numberSuffix;
        }

        $r = Eventy::filter('chapter.label', [$number, $labelPrefix, $this->chapter?->label, $labelSuffix], $this);

        return implode($r);
    }
}
