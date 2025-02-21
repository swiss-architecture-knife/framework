<?php
declare(strict_types=1);

namespace Swark\Ingester\Model\Configuration;

use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class YamlOptionsConfigurer
{
    public function __construct(
        public readonly Filesystem     $yamlPath,
        public readonly string         $filename,
        public readonly OptionsBuilder $optionsBuilder,
    )
    {
    }

    const SOURCE = 'yaml-config';

    public function configure(): void
    {
        $config = Yaml::parseFile($this->yamlPath->path($this->filename));

        if (isset($config['model'])) {
            $this->optionsBuilder->model($config['model'], static::SOURCE);
        }

        if (isset($config['depends-on'])) {
            $this->optionsBuilder->dependsOn($config['depends-on'], static::SOURCE);
        }

        if (isset($config['mapping'])) {
            $this->optionsBuilder->mapping($config['mapping'], static::SOURCE);
        }

        if (isset($config['loaders'])) {
            $this->optionsBuilder->loaders($config['loaders'], static::SOURCE);
        }
    }
}
