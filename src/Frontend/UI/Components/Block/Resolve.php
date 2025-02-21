<?php

namespace Swark\Frontend\UI\Components\Block;

use Illuminate\View\Component;
use Swark\Cms\Chapters\Item;
use TorMorten\Eventy\Facades\Eventy;

/**
 * Resolves the correct view/component for starting a chapter
 */
class Resolve extends Component
{
    public function __construct(
        public readonly ?Item $chapter,
    )
    {
    }


    public function render()
    {
        return function ($data) {
            $path = '~' . $this->chapter->id;

            $viewResolver = function ($path) {
                // if a blade view exists for this path, use that one
                if (app('view')->exists('swark::' . $path)) {
                    return $path;
                }

                // assume that we want to load content
                return 'components.block.extended';
            };

            $viewResolver = Eventy::filter('block.view_resolver', $viewResolver, $this);
            $view = $viewResolver($path);

            $args = ['path' => $path];
            $args = Eventy::filter('block.args', $args, $this);

            return swark_view($view, $args);
        };
    }
}
