<?php

namespace Swark\Cms\Infrastructure\Exchange\Filesystem;

use Illuminate\Support\Carbon;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Swark\Cms\Domain\Factory\MarkdownFactory;
use Swark\Cms\Domain\Model\Exchange\ContentImportResult;
use Swark\Cms\Domain\Model\Exchange\ContentImportResultBuilder;
use Swark\Cms\Domain\Model\Exchange\ContentImportStatus;
use Swark\Cms\Infrastructure\Exchange\Database\DatabaseContentHandler;
use Swark\Cms\Infrastructure\Exchange\Database\ImportableDatabaseContent;

class ContentDirectoryImporter
{
    private ?string $scompPrefix = null;
    private ?string $absolutePath = null;

    public function __construct(public readonly \SplFileInfo      $baseDirectory,
                                private ImportableDatabaseContent $importableDatabaseContent,
    )
    {
        $this->absolutePath = $this->baseDirectory->getRealPath();
        $this->scompPrefix = $this->baseDirectory->getFilename();
    }

    public function import(): \Generator
    {
        // load existing scomp ids for this content type
        $listOfKnownScompIds = $this->importableDatabaseContent->findKnownContent($this->scompPrefix);

        $directoryIterator = new RecursiveDirectoryIterator($this->baseDirectory->getPathname());
        $iteratorIterator = new RecursiveIteratorIterator($directoryIterator);

        // iterate over each file in e.g content/strategy
        /** @var \SplFileInfo $contentFile */
        foreach ($iteratorIterator as $contentFile) {
            if (!$contentFile->isFile()) {
                continue;
            }

            $result = $this->importFile(new \SplFileInfo($contentFile->getPathname()), $listOfKnownScompIds);
            yield $result;
        }
    }

    protected function importFile(\SplFileInfo $contentFile, array &$listOfKnownScompIdsInType): ContentImportResult
    {
        $path = $this->scompPrefix . str_replace($this->absolutePath, "", $contentFile->getRealPath());
        $resultBuilder = ContentImportResultBuilder::of($path);

        yo_info('Trying to import content file %s', [$path], 'import.content.start');

        $filename = $contentFile->getFilename();
        $exploded = explode(".", $filename);
        $scompSuffix = $exploded[0];
        $scompId = $this->scompPrefix . "_" . $scompSuffix;

        $fileSuffix = array_pop($exploded);
        $knownSuffixes = ['md' => 'markdown', 'html' => 'html', 'txt' => 'html'];

        if (!isset($knownSuffixes[$fileSuffix])) {
            return $resultBuilder->make(ContentImportStatus::WARNING, sprintf("Ignoring %s: Unknown file extension '%s'", $filename, $fileSuffix), 'import.content.ignored');
        }

        $targetContentType = $knownSuffixes[$fileSuffix];

        $markdownWithFrontmatter = MarkdownFactory::read($contentFile->getPathname());

        if (isset($listOfKnownScompIdsInType[$scompId])) {
            $lastUpdatedAt = $listOfKnownScompIdsInType[$scompId];

            if (!isset($markdownWithFrontmatter->frontmatter['updated_at'])) {
                return $resultBuilder->make(ContentImportStatus::INFO, sprintf("Skipping content %s: Already inside database and raw content not marked with :updated_at", $scompId), 'import.content.skipped_already_in_database');
            }

            $date = Carbon::parse($markdownWithFrontmatter->frontmatter['updated_at']);

            if (!($isContentUpdatedAfterDatabaseUpdate = $date->isAfter($lastUpdatedAt))) {
                return $resultBuilder->make(ContentImportStatus::WARNING, sprintf("Skipping content %s: Marked as updated at %s, but in database it is already updated at %s", $scompId, $date, $lastUpdatedAt), 'import.content.skipped_already_updated');
            }
        }

        $localContent = new LocalContent($scompId, $markdownWithFrontmatter->content, $targetContentType);
        $content = $this->importableDatabaseContent->import($localContent);

        // make sure that bla.txt and bla.md cannot exist at the same time
        $listOfKnownScompIdsInType[$content->scomp_id] = $content;
        return $resultBuilder->make(ContentImportStatus::SUCCESS, null, 'import.content.success');
    }
}
