<?php

namespace Swark\Cms\Domain\Model;

use Swark\Cms\Domain\Model\Block\BlockCollection;
use Swark\Cms\Domain\Model\Chapters\Chapters;
use Swark\Cms\Domain\Model\Page\Configuration;
use Swark\Cms\Domain\Model\Page\Fragments;

class Page
{
    public function __construct(
        public readonly string          $resourcePath,
        public readonly BlockCollection $blocks,
        public readonly Chapters        $chapters,
        public readonly Fragments       $fragments,
        public readonly Configuration   $configuration,
    )
    {
    }

    public function __($langKey, ?string $default = null)
    {
        if (isset($this->fragments[$langKey])) {
            return $this->fragments[$langKey];
        }

        // TODO: Find language key by current route
        $key = 'swark::' . '$route->name' . '.' . $langKey;

        $r = __($key);
        $r = $r == $key && $default ? $default : $r;

        return $r;
    }
}
