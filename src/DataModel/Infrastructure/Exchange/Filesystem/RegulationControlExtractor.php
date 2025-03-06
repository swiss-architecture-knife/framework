<?php

namespace Swark\DataModel\Infrastructure\Exchange\Filesystem;

use Swark\Cms\Domain\Factory\MarkdownFactory;

/**
 * Extract frontmatter and content for a regulation control from a YAML file, e.g. regulations/nis2/1/controls/2.yaml
 */
class RegulationControlExtractor
{
    public function __construct(public readonly \SplFileInfo $controlYamlFrontmatterFile)
    {
    }

    public function extract(): array
    {
        $markdownWithFrontmatter = MarkdownFactory::read($this->controlYamlFrontmatterFile->getRealPath());

        return [
            [
                'external_id' => $markdownWithFrontmatter->frontmatter['external_id'],
            ],
            [
                'name' => $markdownWithFrontmatter->frontmatter['name'],
                'content' => $markdownWithFrontmatter->content,
            ]
        ];
    }
}
