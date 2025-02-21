<?php

namespace Swark\Frontend\UI\Components\Outline;

use Illuminate\View\Component;
use Swark\Cms\Chapters\Chapters;


/**
 * Creates recursively a Table of Contents
 */
class Outline extends Component
{
    public function __construct(
        public readonly Chapters $chapters,
    )
    {
    }

    public function render()
    {
        $args['chapters'] = $this->chapters;

        return swark_view_auto($args, 'components.outline.outline');
    }
}
