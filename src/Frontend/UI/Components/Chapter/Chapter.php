<?php

namespace Swark\Frontend\UI\Components\Chapter;

use Illuminate\View\Component;
use Swark\Cms\Chapters\Item;
use TorMorten\Eventy\Facades\Eventy;

/**
 * Inlineable chapter
 */
class Chapter extends Component
{
    public function __construct(
        public readonly Item $chapter,
        public readonly mixed $context = null,
    )
    {
    }

    public function render()
    {
        $useContext = Eventy::filter('chapter.context', [
            'chapter' => $this->chapter,
            'context' => $this->context
        ]);

        return swark_view_auto([
            'chapter' => $this->chapter,
            'context' => $useContext,
        ], 'components.chapter.chapter');
    }
}
