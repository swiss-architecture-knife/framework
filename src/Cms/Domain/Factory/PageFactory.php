<?php

namespace Swark\Cms\Domain\Factory;

use Swark\Cms\Domain\Model\Block\BlockCollection;
use Swark\Cms\Domain\Model\Chapters\Chapters;
use Swark\Cms\Domain\Model\Content;
use Swark\Cms\Domain\Model\ContentType;
use Swark\Cms\Domain\Model\Page;
use Swark\Cms\Domain\Model\Page\Configuration;
use Swark\Cms\Domain\Model\Page\Fragments;
use Swark\Kernel\Infrastructure\Facades\Cms;
use Symfony\Component\Yaml\Yaml;

class PageFactory
{
    /**
     * Find a JSON or YAML configuration file in the current route's content folder
     */
    const RESOURCE_NAME_CURRENT_CONFIG = '~page:config';

    /**
     * Create a new page with the given ToC chapters
     * @param string $resourcePath
     * @param BlockCollection $blockCollection
     * @param Chapters $toc
     * @param array $fragments additional fragments to pass into the content
     * @return Page
     */
    public function create(string          $resourcePath,
                           BlockCollection $blockCollection,
                           Chapters        $toc,
                           array           $fragments = []): Page
    {
        $configContents = Cms::findExactly(static::RESOURCE_NAME_CURRENT_CONFIG);
        $pageConfig = [];

        /** @var Content $configPrimary */
        if ($configPrimary = $configContents->first()) {
            // ~/page.yaml is present
            if ($configPrimary->body->contentType() == ContentType::YAML) {
                $pageConfig = Yaml::parse($configPrimary->body->raw());
            }
        }

        // TODO Make this a proper factory class
        $config = Configuration::create($pageConfig);

        // TODO: Fire event to make it more customizable

        return new Page(
            $resourcePath,
            $blockCollection,
            chapters: $config->mergeToc($toc),
            fragments: new Fragments($config->mergeFragments($fragments)),
            configuration: $config,
        );
    }
}
