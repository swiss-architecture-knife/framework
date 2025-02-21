<?php

namespace Swark\Services\Data\Filesystem\Import;

use DirectoryIterator;
use Illuminate\Support\Carbon;
use Swark\Content\Domain\Factory\MarkdownFactory;
use Swark\DataModel\Content\Domain\Entity\Content;
use Swark\Services\Data\Importable;

/**
 * Import static content.
 */
class ContentDirectoryImporter implements Importable
{
    private ?string $scompPrefix = null;

    public function __construct(public readonly \SplFileInfo $baseDirectory,
    )
    {
        $this->scompPrefix = $this->baseDirectory->getFilename();
    }

    public function import()
    {
        // iterate over each file in e.g content/strategy
        foreach (new DirectoryIterator($this->baseDirectory->getPathname()) as $contentFile) {
            if ($contentFile->isDot() || $contentFile->isDir()) {
                continue;
            }

            // load existing scomp ids for this content type
            $listOfKnownScompIds = Content::whereLike('scomp_id', $this->scompPrefix . '%')->pluck('updated_at', 'scomp_id')->all();

            $this->importContent(new \SplFileInfo($contentFile->getPathname()), $listOfKnownScompIds);
        }
    }

    protected function importContent(\SplFileInfo $contentFile, array &$listOfKnownScompIdsInType)
    {
        yo_info('Trying to import content file %s', [$contentFile->getRealPath()], 'import.content.start');

        $filename = $contentFile->getFilename();
        $exploded = explode(".", $filename);
        $scompSuffix = $exploded[0];
        $scompId = $this->scompPrefix . "_" . $scompSuffix;

        $fileSuffix = array_pop($exploded);
        $knownSuffixes = ['md' => 'markdown', 'html' => 'html', 'txt' => 'html'];

        if (!isset($knownSuffixes[$fileSuffix])) {
            return yo_warn("Ignoring %s: Unknown file extension %s", [$filename, $fileSuffix], 'import.content.ignored');
        }

        $targetContentType = $knownSuffixes[$fileSuffix];

        $markdownWithFrontmatter = MarkdownFactory::read($contentFile->getPathname());

        if (isset($listOfKnownScompIdsInType[$scompId])) {
            $lastUpdatedAt = $listOfKnownScompIdsInType[$scompId];

            if (!isset($markdownWithFrontmatter->frontmatter['updated_at'])) {
                return yo_debug("Skipping content %s: Already inside database and raw content not marked with updated_at", [$scompId], 'import.content.skipped_already_in_database');
            }

            $date = Carbon::parse($markdownWithFrontmatter->frontmatter['updated_at']);

            if (!($isContentUpdatedAfterDatabaseUpdate = $date->isAfter($lastUpdatedAt))) {
                return yo_warn("Skipping content %s: Marked as updated at %s, but in database it is already updated at %s", [$scompId, $date, $lastUpdatedAt], 'import.content.skipped_already_updated');
            }
        }

        $content = Content::updateOrCreate([
            'scomp_id' => $scompId
        ], [
            'content' => $markdownWithFrontmatter->content,
            'type' => $targetContentType,
        ]);

        // make sure that bla.txt and bla.md cannot exist at the same time
        $listOfKnownScompIdsInType[$content->scomp_id] = $content;
    }
}
