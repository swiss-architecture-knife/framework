<?php

namespace Swark\Services\Data\Filesystem\Import;

use Swark\Content\Domain\Factory\MarkdownFactory;
use Swark\DataModel\Compliance\Domain\Model\RelevanceType;

/**
 * Extract frontmatter and content for a regulation chapter from multiple YAML files, e.g.
 * <ul>
 *     <li>regulations/nis2/1/law.md</li>
 * <li>regulations/nis2/1/summary.md</li>
 * <li>regulations/nis2/1/actual.md</li>
 * <li>regulations/nis2/1/target.md</li>
 * </ul>
 */
class RegulationChapterExtractor
{
    const LAW_MARKDOWN = 'law.md';
    const SUMMARY_MARKDOWN = 'summary.md';
    const ACTUAL_STATUS = 'actual.md';
    const TARGET_STATUS = 'target.md';


    public function __construct(public readonly \SplFileInfo $baseDirectoryOfChapter)
    {
    }

    /**
     * @return array[]
     * @throws \Exception
     */
    public function extract(): array
    {
        $must = [
            'external_id' => basename($this->baseDirectoryOfChapter->getRealPath()) // chapter ID
        ];

        $args = [];

        $path = $this->baseDirectoryOfChapter->getRealPath();

        // swark itself provides those regulations those content is defined in laws
        if ($dataLaw = MarkdownFactory::read($path . '/' . static::LAW_MARKDOWN)) {
            $args['name'] = $dataLaw->frontmatter['heading'];
            $args['official_content'] = $dataLaw->content;
            $args['relevancy'] = match ($dataLaw->frontmatter['relevancy']) {
                'no' => RelevanceType::NONE,
                'yes' => RelevanceType::HIGH,
                'partly' => RelevanceType::MIDDLE,
                default => null
            };
        }

        // "Summary" property is provided by swark
        if ($dataSummary = MarkdownFactory::read($path . '/' . static::SUMMARY_MARKDOWN)) {
            $args['summary'] = $dataSummary->content;
        }

        //  "Actual status" and "Target status" is provided by the user: This is the information will be gathered when analysing the user's world
        if ($actual = MarkdownFactory::read($path . '/' . self::ACTUAL_STATUS)) {
            $args['actual_status'] = $actual->content;
        }

        if ($target = MarkdownFactory::read($path . '/' . self::TARGET_STATUS)) {
            $args['target_status'] = $target->content;
        }

        return [$must, $args];
    }
}
