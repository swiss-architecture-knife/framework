<?php

namespace Swark\Cms\Application\Exchange;

use Swark\Cms\Infrastructure\Exchange\Database\DatabaseContentHandler;
use Swark\Cms\Infrastructure\Exchange\Database\ImportableDatabaseContent;
use Swark\Cms\Infrastructure\Exchange\Filesystem\ContentDirectoryImporter;
use Swark\Cms\Infrastructure\Exchange\Filesystem\ImportableRootContentScanner;

readonly class ContentImportJob
{
    public function __construct(
        private ContentImportOptions   $options,
    )
    {
    }

    public function run(): array
    {
        $r = [];

        $scanner = new ImportableRootContentScanner($this->options->rootDirectory);
        $directories = $scanner->scan();

        // e.g. content/strategy/
        /** @var \SplFileInfo $contentTypeDirectory */
        foreach ($directories as $directory) {
            $importer = app()->make(ContentDirectoryImporter::class, [
                'baseDirectory' => $directory,
                'databaseContentHandler' => app()->make(ImportableDatabaseContent::class),
            ]);

            foreach ($importer->import() as $importStatus) {
                $r[] = $importStatus;

            }
        }

        return $r;
    }
}
