<?php

namespace Swark\Frontend\Infrastructure\Hooking;

use Swark\Frontend\Infrastructure\View\RoutableViewFinder;
use Swark\Kernel\Infrastructure\Container\Attributes\FirstTagged;
use TorMorten\Eventy\Facades\Eventy;

class RenderContentOnHooksRegistrar
{
    public function __construct(
        public readonly array              $mappings,
        #[FirstTagged('content-finder')]
        public readonly RoutableViewFinder $routableViewFinder,
        public readonly string             $suffix = ':splat',)
    {
    }

    public function register()
    {
        foreach ($this->mappings as $event => $fileSuffix) {
            // check if routable view with chapter name is available
            Eventy::addAction($event . $this->suffix, function ($args) use ($fileSuffix) {
                $chapter = $args['chapter'];
                $relativePath = '~' . $chapter->id . '-' . $fileSuffix;

                if (content_exists($relativePath)) {
                    echo content($relativePath, $args)->render();
                }
            }, 10, 2);
        }
    }
}
