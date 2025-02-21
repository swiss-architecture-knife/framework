<?php

namespace Swark\Cms\Events;

use Swark\Frontend\Infrastructure\View\RoutableConfigurationItem;
use Swark\Frontend\Infrastructure\View\RoutableViewFinder;
use TorMorten\Eventy\Facades\Eventy;

/**
 * Based upon the current route this registrar registers custom events
 */
class DelegatingHooksRegistrar
{
    public function __construct(
        public readonly RoutableViewFinder        $finder,
        public readonly RoutableConfigurationItem $routableConfigurationItem,
        public readonly array                     $eventyKnownEvents,
        public readonly string                    $suffix = ':splat',
    )
    {
    }

    /**
     * Register delegated events so that one can listen to the actual route and chapter and not only on the generic events.
     * The parent application can listen to those and customize the output.
     * @return void
     */
    public function registerDelegatedEvents()
    {
        $routeName = $this->routableConfigurationItem->getRouteName();
        $routeArgs = $this->routableConfigurationItem->getSearchPaths(false);

        foreach ($this->eventyKnownEvents as $incomingEvent => $delegatedEvent) {
            Eventy::addAction($incomingEvent . $this->suffix, function(array $args) use($routeName, $routeArgs, $incomingEvent) {
                $actionWithRouteArgs = $routeName . '.' . implode('.', $routeArgs);
                $action = $routeName . '-' . $incomingEvent;

                Eventy::action($actionWithRouteArgs, $args, 20, 2);
                Eventy::action($action, $args, 10, 2);
            }, 10, 2);
        }
    }
}
