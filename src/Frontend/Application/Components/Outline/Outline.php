<?php

namespace Swark\Frontend\Application\Components\Outline;

use Illuminate\View\Component;
use Swark\Cms\Domain\Model\Chapters\Chapters;


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
