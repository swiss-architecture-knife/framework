<?php

namespace Swark\Cms;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ResourceName
{
    const PATH_SEPARATOR = '/';

    const RELATIVE_LOCATION_MARKER = '~';
    const SEGMENT_SEPARATOR = '__';

    const SWARK_ROUTE_PREFIX = 'swark';

    public function __construct(
        public readonly string  $requested,
        public readonly string  $full,
        public readonly array   $parts,
        public readonly array   $parents,
        public readonly string  $child,
        public readonly ?string $fileset = null,
    )
    {
    }

    public function parentToPath(?string $pathSeparator = null): string
    {
        return implode($pathSeparator ?? static::PATH_SEPARATOR, $this->parents);
    }

    public function toPath(?string $pathSeparator = null): string
    {
        return implode($pathSeparator ?? static::PATH_SEPARATOR, $this->parts);
    }

    public static function of(string $resourceName): ResourceName
    {
        throw_if(empty($resourceName), "Resource name cannot be empty");
        $requested = $resourceName;
        $resolved = $requested;

        if (Str::startsWith($requested, static::RELATIVE_LOCATION_MARKER)) {
            $resolved = Route::getCurrentRequest()->path();
        }

        // remove any leading/trailing slashes and the route prefix
        $resolved = preg_replace('/^(\/)*(' . static::SWARK_ROUTE_PREFIX . '\/*)?(.*)(\/)*$/', '$3', $resolved);
        if (Str::startsWith($requested, static::RELATIVE_LOCATION_MARKER)) {
            // if this is relative to the current URL, append the relative component the resource name
            $requested = preg_replace('/^' . preg_quote(static::RELATIVE_LOCATION_MARKER) . '(\/)*(.*)$/', '$2', $requested);

            if (strlen(trim($requested)) > 0) {
                $resolved .= static::PATH_SEPARATOR . $requested;
            }
        }

        $fileset = null;

        if (preg_match('/^(.*)\:(\w+)$/', $resolved, $matches)) {
            $fileset = $matches[2];
            $resolved = preg_replace('/^(.*)\:(\w+)$/', '$1', $resolved);
        }

        // replace any URL path segment with our scomp-compatible segment separator
        $resolved = str_replace(static::PATH_SEPARATOR, static::SEGMENT_SEPARATOR, $resolved);

        $parts = explode(static::SEGMENT_SEPARATOR, $resolved);
        $child = '';
        $parents = [];

        if (sizeof($parts) >= 1) {
            $child = $parts[sizeof($parts) - 1];
        }

        if (sizeof($parts) >= 1) {
            $parents = array_slice($parts, 0, sizeof($parts) - 1);
        }

        return new ResourceName(
            requested: $resourceName,
            full: $resolved,
            parts: $parents,
            parents: $parents,
            child: $child,
            fileset: $fileset
        );
    }

    /**
     * @param array $resourceNames
     * @return ResourceName[]
     */
    public static function ofMany(array $resourceNames): array
    {
        return collect($resourceNames)->map(fn($item) => static::of($item))->toArray();
    }

    public function __toString()
    {
        return $this->full;
    }
}
