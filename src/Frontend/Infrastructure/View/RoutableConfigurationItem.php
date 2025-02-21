<?php

namespace Swark\Frontend\Infrastructure\View;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

/**
 * Provide local file mappings for routes which are assigned to configuration items
 */
class RoutableConfigurationItem
{
    public function __construct(private ?\Illuminate\Routing\Route $route = null)
    {
    }

    public function getRoute(): ?Route
    {
        if (is_null($this->route)) {
            $this->route = \Illuminate\Support\Facades\Route::current();
        }

        return $this->route;
    }

    public function getRouteName(): ?string
    {
        return $this->getRoute()?->getName();
    }

    public function getParameters(): array
    {
        return $this->getRoute()?->parameters() ?? [];
    }

    private ?string $relativePath = null;

    public function getRelativePath(): string
    {
        if ($this->relativePath) {
            return $this->relativePath;
        }

        $currentRoute = $this->getRouteName();
        $currentRoute = str_replace('swark.', '', $currentRoute);
        $this->relativePath = str_replace('.', '/', $currentRoute);

        return $this->relativePath;
    }

    private array $searchPaths = ['with_scomp' => null, 'without_scomp' => null];

    /**
     * Checks the parameters and converts them to a string. This is required to cast resolved objects (e.g. a Policy or Strategy) to its primary id or scomp_id.
     * @return array
     */
    public function getSearchPaths(bool $appendScompIdIfAvailable = true): array
    {
        $key = $appendScompIdIfAvailable ? 'with_scomp' : 'without_scomp';

        if (null !== $this->searchPaths[$key]) {
            return $this->searchPaths[$key];
        }

        $parameters = $this->getParameters();
        $r = [];

        foreach ($parameters as $key => $value) {
            if (is_string($value) || is_int($value)) {
                $r[] = $value;
            } else if (is_object($value)) {
                // append primary key from 'id' column
                if ($value instanceof Model) {
                    $r[] = $value->getKey();
                }

                // also append scomp_id if present and requested
                if ($appendScompIdIfAvailable && in_array(HasScompId::class, class_uses_recursive($value::class))) {
                    $r[] = $value->scomp_id;
                }
            }
        }

        $this->searchPaths[$key] = $r;

        return $this->searchPaths[$key];
    }

    public function getRelativeComponentPath(... $args): string {
        return $this->getRelativePath() . '/' . implode('/', $this->getSearchPaths(...$args));
    }
}
