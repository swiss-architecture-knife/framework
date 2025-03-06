<?php

namespace Swark\Cms\Infrastructure\Exchange\Filesystem;

use DirectoryIterator;

class ImportableRootContentScanner
{
    private ?DirectoryIterator $directoryIterator = null;

    public function __construct(string $rootDirectory)
    {
        if (!is_dir($rootDirectory)) {
            throw new \Exception("The directory {$rootDirectory} does not exist");
        }

        $this->directoryIterator = new DirectoryIterator($rootDirectory);
    }

    public function scan(): array
    {
        $r = [];

        /** @var \SplFileInfo $contentTypeDirectory */
        foreach ($this->directoryIterator as $contentTypeDirectory) {
            if ($contentTypeDirectory->isDot() || $contentTypeDirectory->isFile()) {
                continue;
            }

            yo_info('Registering importable static content from %s...', [$contentTypeDirectory], 'import.content.register');

            $r[] = new \SplFileInfo($contentTypeDirectory->getPathname());
        }

        return $r;
    }
}
