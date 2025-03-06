<?php

namespace Swark\DataModel\Infrastructure\Exchange\Filesystem;

use DirectoryIterator;
use Swark\DataModel\Infrastructure\Exchange\Importable;

/**
 * Import markdown files from the local filesystem
 */
class DataModelFilesystemImporter implements Importable
{

    const SUBDIRECTORY_REGULATIONS = 'regulations';

    private array $importables = [];

    public function __construct(
        public readonly string $rootDirectory,
        public readonly bool   $enableRegulations,
    )
    {
        if (!is_dir($this->rootDirectory)) {
            throw new \Exception("Root directory '{$this->rootDirectory}' does not exist");
        }
        $this->configure();
    }

    private function configure()
    {
        if ($this->enableRegulations) {
            $this->configureRegulations();
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
        $splFile = new \SplFileInfo($this->rootDirectory . '/' . static::SUBDIRECTORY_REGULATIONS);

        if (!$splFile->isDir()) {
            return;
        }

        $regulationsDirectoryIterator = new DirectoryIterator($splFile->getRealPath());

        // e.g. regulations/nis2/
        foreach ($regulationsDirectoryIterator as $regulationDirectory) {
            if ($regulationDirectory->isDot() || $regulationDirectory->isFile()) {
                continue;
            }

            yo_info('Registering directory %s for import...', [$regulationDirectory->getRealPath(), $regulationDirectory], 'import.regulation.register');

            $this->importables[] = app()->make(RegulationDirectoryImporter::class, [
                'regulationsBaseDirectory' => new \SplFileInfo($splFile->getRealPath()),
                'regulation' => $regulationDirectory->getFilename(),
            ]);
        }
    }

    public function importables(): array
    {
        return $this->importables;
    }

    public function import()
    {
        foreach ($this->importables as $importable) {
            $importable->import();
        }
    }
}
