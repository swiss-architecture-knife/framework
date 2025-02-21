<?php

namespace Swark\Services\Data;

/**
 * Options for importing data into swark
 */
class ImportOptions
{
    private array $withOptions = [];

    const DEFAULT = 'default';
    const CONTENT = 'content';
    const REGULATIONS = 'regulations';
    const STRATEGIES = 'strategies';
    const INFRASTRUCTURE = 'infrastructure';
    const RULES = 'rules';
    const DEFAULT_EXCEL_FILE_TO_IMPORT = 'import.xlsx';

    private ?string $rootDirectory = null;
    private ?string $excelFilename = null;

    public function __construct(string $path, array $withOptions = [])
    {
        $this->configureOptions($withOptions);
        $this->configureLocalEnvironment($path);
    }

    private function configureOptions(array $withOptions = [])
    {
        $allOptions = static::getAvailableOptions();
        $targetOptions = [];

        foreach ($withOptions as $withOption) {
            if (in_array($withOption, $allOptions)) {
                $targetOptions[] = $withOption;
            }
        }

        if (empty($targetOptions)) {
            $targetOptions = $allOptions;
        }

        // add default options
        if (!in_array(static::DEFAULT, $targetOptions)) {
            array_unshift($targetOptions, static::DEFAULT);
        }

        $this->withOptions = $targetOptions;
    }

    /**
     * Configure local environment context
     * @param string $path
     * @return void
     */
    private function configureLocalEnvironment(string $path)
    {
        $absolutePath = realpath($path);
        $excelFilename = self::DEFAULT_EXCEL_FILE_TO_IMPORT;

        // path to Excel file
        if (is_file($absolutePath)) {
            $excelFilename = basename($absolutePath);
            $absolutePath = dirname($absolutePath);
        }

        $this->rootDirectory = $absolutePath;
        $this->excelFilename = $excelFilename;
    }

    /**
     * Return a new DirectoryIterator of a subdirectory of this root directory.
     *
     * @param string $subDirectory
     * @return \DirectoryIterator|null
     */
    public function directoryIterator(string $subDirectory): ?\DirectoryIterator
    {
        $path = $this->rootDirectory() . '/' . $subDirectory;

        if (!is_dir($path)) {
            return yo_debug("Missing directory %s", [$path], 'import.directory');
        }

        return new \DirectoryIterator($path);
    }

    public function rootDirectory(): string
    {
        return $this->rootDirectory;
    }

    public function excelFilePath(): \SplFileInfo
    {
        $r = new \SplFileInfo($this->rootDirectory() . '/' . $this->excelFilename);
        return $r;
    }

    /**
     * Return true if option has been enabled
     *
     * @param string $option
     * @return bool
     */
    public function has(string $option): bool
    {
        return in_array($option, $this->withOptions);
    }

    public static function getAvailableOptions(): array
    {
        return [
            static::DEFAULT,
            static::CONTENT,
            static::REGULATIONS,
            static::STRATEGIES,
            static::INFRASTRUCTURE,
            static::RULES
        ];
    }
}
