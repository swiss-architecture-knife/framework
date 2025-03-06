<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Import;

/**
 * Options for importing the data model into swark
 */
class DataModelImportOptions
{
    private array $enabledImportCategories = [];

    const DEFAULT = 'default';
    const CONTENT = 'content';
    const COMPLIANCE = 'compliance';
    const GOVERNANCE = 'governance';

    const INFRASTRUCTURE = 'it';
    const AUDITING = 'auditing';
    const DEFAULT_EXCEL_FILE_TO_IMPORT = 'import.xlsx';

    private ?string $rootDirectory = null;
    private ?string $excelFilename = null;

    public function __construct(
        public readonly string $pathToImportDirectory,
        public readonly bool   $markdownOnly = false,
        array                  $enabledImportCategories = [])
    {
        $this->configureEnabledImports($enabledImportCategories);
        $this->configureLocalEnvironment($pathToImportDirectory);
    }

    private function configureEnabledImports(array $enabledImportCategories = [])
    {
        $availableImportCategories = static::getAvailableImportCategories();
        $targetOptions = [];

        foreach ($enabledImportCategories as $enabledCategory) {
            if (in_array($enabledCategory, $availableImportCategories)) {
                $targetOptions[] = $enabledCategory;
            }
        }

        if (empty($targetOptions)) {
            $targetOptions = $availableImportCategories;
        }

        // add default options
        if (!in_array(static::DEFAULT, $targetOptions)) {
            array_unshift($targetOptions, static::DEFAULT);
        }

        $this->enabledImportCategories = $targetOptions;
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
    public function isCategoryEnabled(string $option): bool
    {
        return in_array($option, $this->enabledImportCategories);
    }

    public static function getAvailableImportCategories(): array
    {
        return [
            static::DEFAULT,
            static::CONTENT,
            static::COMPLIANCE,
            static::GOVERNANCE,
            static::INFRASTRUCTURE,
            static::AUDITING
        ];
    }
}
