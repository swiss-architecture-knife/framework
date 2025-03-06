<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Exchange\Ingester;

use Illuminate\Contracts\Filesystem\Filesystem;
use Swark\DataModel\Infrastructure\Exchange\Ingester\Configuration\OptionsBuilder;
use Swark\DataModel\Infrastructure\Exchange\Ingester\Converter\ConverterFactory;
use Symfony\Component\Yaml\Yaml;

class IngesterYamlGlobalConfiguration
{
    public function __construct(
        public readonly Filesystem $yamlPath,
        public readonly string     $filename,
    )
    {
    }

    public function configure(ConverterFactory $converterFactory): void
    {
        $config = Yaml::parseFile($this->yamlPath->path($this->filename));

        OptionsBuilder::createConverters(
            $converterFactory,
            $config,
            fn($alias, $options) => $converterFactory->locate($alias, $options)
        );
    }
}
