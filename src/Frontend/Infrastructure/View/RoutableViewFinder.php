<?php

namespace Swark\Frontend\Infrastructure\View;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\View\FileViewFinder;

class RoutableViewFinder extends FileViewFinder
{
    protected string $pageLayout = 'page-layout';

    protected $extensions = ['blade.php', 'html'];

    protected string $routedViewMarker = '~';

    protected string $wildcardCharacterInPath = '_';

    public function __construct(public readonly RoutableConfigurationItem $routableConfigurationItem, Filesystem $files, array $paths, ?array $extensions = null) {
        parent::__construct($files, $paths, $extensions);
    }

    public const string VIEW_NAME_REGEX = '/^([a-zA-z0-9\-\.]+)(:[\w\,]+)?$/';

    /**
     * Instead of "just" relying upon one directory, we include multiple options with priority in the following order:
     * <ul>
     *     <li>index/_/route-arg2/page-layout.blade.php (only second route arg must match)</li>
     *     <li>index/route-arg1/route-arg2/page-layout.blade.php (exact match)</li>
     *     <li>index/route-arg1/page-layout.blade.php (must match with first route arg)</li>
     *     <li>index/page-layout.blade.php</li>
     *     <li>index.blade.php</li>
     * </ul>
     *
     * @param string $name
     * @return array
     */
    protected function getPossibleViewFiles($name)
    {
        $currentRoute = $this->routableConfigurationItem->getRouteName();

        if (!Str::startsWith($name, $this->routedViewMarker) || !$currentRoute) {
            return parent::getPossibleViewFiles($name);
        }

        $fileToLookFor = $name == $this->routedViewMarker ? $this->pageLayout : Str::replace('~', '', $name);

        if (!preg_match(static::VIEW_NAME_REGEX, $fileToLookFor)) {
            throw new \Exception(("Invalid pattern for file to resolve: '" . $fileToLookFor));
        }

        $parts = explode(":", $fileToLookFor);
        $useExtensions = [];

        if (sizeof($parts) >= 2)  {
            $fileToLookFor = $parts[0];
            $useExtensions = explode(",", $parts[1]);
        }

        $parameterValues = $this->routableConfigurationItem->getSearchPaths();
        $splittedPrefix = $this->routableConfigurationItem->getRelativePath();

        // wildcards
        $candidates = array_map(fn($wildcardPath) => $splittedPrefix . $wildcardPath . '/' . $fileToLookFor, $this->createWildcardPath($parameterValues));

        // index/page-layout.blade.php
        $candidates[] = $splittedPrefix . '/' . $fileToLookFor;

        // also search for index.blade.php if we are just using '~'
        if ($name == $this->routedViewMarker) {
            // index.blade.php
            $candidates[] = $splittedPrefix;
        }

        $r = [];

        $useExtensions += $this->extensions;

        foreach ($useExtensions as $extension) {
            foreach ($candidates as $candidate) {
                $r[] = $candidate . '.' . $extension;
            }
        }

        return $r;
    }

    protected function findInPaths($name, $paths)
    {
        $tested = [];

        foreach ((array)$paths as $path) {
            foreach ($this->getPossibleViewFiles($name) as $file) {
                $viewPath = $path . '/' . $file;
                $tested[] = $viewPath;

                if (strlen($viewPath) < (PHP_MAXPATHLEN - 1) && $this->files->exists($viewPath)) {
                    return $viewPath;
                }
            }
        }

        $ex = Str::startsWith($name, $this->routedViewMarker) ? 'Routable view not found' : 'View for [' . $name . '] not found';

        throw new \InvalidArgumentException("$ex. Tested search paths: " . implode(',', $tested));
    }

    /**
     * Creates a list of wildcard paths. This is a little bit optimized to not consider *every* path combination but the most common ones.
     * @param array $parameters
     * @return array
     */
    protected function createWildcardPath(array $parameters = []): array
    {
        $r = [];
        $totalParameters = sizeof($parameters);

        if (!$totalParameters) {
            return $r;
        }

        // if we have more than one parameter, make a wildcard for each of parameter but the last one.
        // we assume, that the last one has a unique ID in the whole application and is not in an m-n-relationship.
        // index/_/_/route-parameter-3/page-layout
        if ($totalParameters > 1) {
            $r[] = str_repeat('/' . $this->wildcardCharacterInPath, $totalParameters - 1) . '/' . $parameters[$totalParameters - 1];
        }

        // use the most specific path route so that each of the parameters matches
        // index/route-parameter-1/route-parameter-2/route-parameter-3/page-layout
        $r[] = '/' . implode('/', $parameters);

        // move from last route parameter to first one:
        // index/route-parameter-1/route-parameter-2/page-layout
        // index/route-parameter-1/page-layout
        if ($totalParameters > 1) {
            for ($i = $totalParameters - 2; $i >= 0; $i--) {
                $r[] = str_repeat('/' . $this->wildcardCharacterInPath, $i) . '/' . $parameters[$i];
            }
        }

        return $r;
    }
}
