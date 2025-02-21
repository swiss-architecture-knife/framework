<?php
declare(strict_types=1);

namespace Swark\Ingester;

use Illuminate\Contracts\Filesystem\Filesystem;
use Swark\Ingester\Model\Configuration\OptionsBuilder;
use Swark\Ingester\Model\Converter\ConverterFactory;
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
