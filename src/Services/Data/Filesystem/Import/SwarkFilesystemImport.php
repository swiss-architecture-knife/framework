<?php

namespace Swark\Services\Data\Filesystem\Import;

use Swark\Services\Data\Importable;
use Swark\Services\Data\ImportOptions;
use TorMorten\Eventy\Facades\Eventy;

/**
 * Import markdown files from the local filesystem
 */
class SwarkFilesystemImport implements Importable
{

    const SUBDIRECTORY_REGULATIONS = 'regulations';

    const SUBDIRECTORY_CONTENT = 'content';

    private array $importables = [];

    public function __construct(
        public readonly ImportOptions $options,
    )
    {
        $this->configure();
    }

    private function configure()
    {
        if ($this->options->has(ImportOptions::REGULATIONS)) {
            $this->configureRegulations();
        }

        if ($this->options->has(ImportOptions::CONTENT)) {
            $this->configureContent();
        }
    }

    /**
     * Configure importable regulations/controls
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function configureRegulations()
    {
        // regulations/
        $baseDir = $this->options->directoryIterator(static::SUBDIRECTORY_REGULATIONS);

        if (!$baseDir) {
            return;
        }

        // e.g. regulations/nis2/
        foreach ($baseDir as $regulationDirectory) {
            if ($regulationDirectory->isDot() || $regulationDirectory->isFile()) {
                continue;
            }

            yo_info('Registering directory %s for import...', [$regulationDirectory->getRealPath(), $regulationDirectory], 'import.regulation.register');

            $this->importables[] = app()->make(RegulationDirectoryImporter::class, [
                'baseDirectory' => new \SplFileInfo($regulationDirectory->getPathname())
            ]);
        }
    }

    /**
     * Configure importable content
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function configureContent()
    {
        // content/
        $baseDir = $this->options->directoryIterator(static::SUBDIRECTORY_CONTENT);

        if (!$baseDir) {
            return;
        }

        // e.g. content/strategy/
        foreach ($baseDir as $contentTypeDirectory) {
            if ($contentTypeDirectory->isDot() || $contentTypeDirectory->isFile()) {
                continue;
            }

            yo_info('Registering importable static content from %s...', [$contentTypeDirectory], 'import.content.register');

            $this->importables[] = app()->make(ContentDirectoryImporter::class, [
                'baseDirectory' => new \SplFileInfo($contentTypeDirectory->getPathname())
            ]);
        }
    }

    public function import()
    {
        foreach ($this->importables as $importable) {
            $importable->import();
        }
    }
}
