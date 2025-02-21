<?php

namespace Swark\Frontend\UI\Components\Chapter;

use Illuminate\View\Component;
use Swark\Cms\Chapters\Item;
use TorMorten\Eventy\Facades\Eventy;

/**
 * Creates a header for a chapter
 */
class ChapterHeader extends Component
{
    public function __construct(
        public readonly Item $chapter,
    )
    {
    }

    public function render()
    {
        $args['heading_level'] = $this->chapter->depth + 2;
        $args['chapter_id'] = $this->chapter->uid();
        $args['chapter'] = $this->chapter;
        $args['css_classes'] = 'pt-3';
        $args['tag'] = 'h' . $args['heading_level'];

        $r = Eventy::filter('chapter.header', $args, $this);

        return swark_view('components.chapter.header', $args);
    }
}
