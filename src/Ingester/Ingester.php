<?php
declare(strict_types=1);

namespace Swark\Ingester;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Swark\Ingester\Model\Configuration\OptionsBuilder;
use Swark\Ingester\Model\Configuration\YamlOptionsConfigurer;
use Swark\Ingester\Model\Context;
use Swark\Ingester\Model\Converter\ConverterFactory;
use Swark\Ingester\Model\Models;
use Swark\Ingester\Sink\Loader\CsvLoader;
use Swark\Ingester\Sink\Loader\ExcelLoader;
use Swark\Ingester\Sink\Uniqueness\CompoundIdentifiersDelegateRepository;

class Ingester
{
    const INGESTER_GLOBAL_CONFIGURATION_YAML = 'ingester.yaml';
    const MODEL_OPTIONS_YAML = 'options.yaml';
    const COLUMN_DELETED_AT = 'deleted-at';
    const COLUMN_UPDATED_AT = 'updated-at';

    public function detectModels(): Models
    {
        $r = new Models();
        $converterFactory = new ConverterFactory();

        $disk = Storage::disk('TBD DISK FILE');

        if ($disk->exists(static::INGESTER_GLOBAL_CONFIGURATION_YAML)) {
            (new IngesterYamlGlobalConfiguration($disk, static::INGESTER_GLOBAL_CONFIGURATION_YAML))->configure($converterFactory);
        }

        $directories = $disk->directories();

        foreach ($directories as $directory) {
            $modelContext = new Context($directory);
            $optionsBuilder = new OptionsBuilder();
            $optionsBuilder->model(
                "\\Swark\\DataModel\\" . Str::ucfirst(Str::camel($directory)), 'default');

            $modelContext->isUsable(true);

            if (!$disk->exists($directory . '/' . self::MODEL_OPTIONS_YAML)) {
                $modelContext->unusableBecause(\Swark\Ingester\Model\StatusFlag::OPTIONS_YAML_MISSING);
            }

            if ($modelContext->isUsable()) {
                try {
                    $yamlLoader = new YamlOptionsConfigurer($disk, $directory . '/' . self::MODEL_OPTIONS_YAML, $optionsBuilder);
                    $yamlLoader->configure();
                } catch (IngesterException $e) {
                    $modelContext->unusableBecause($e->modelStatusFlag);
                }
            }

            $options = $optionsBuilder->build($modelContext, $converterFactory);
            $modelContext->options($options);

            foreach ($options->attributeMappings as $attribute) {
                $modelContext->attributes()->add($attribute);
            }

            $loaderDelegate = new CompoundIdentifiersDelegateRepository();

            if ($modelContext->isUsable()) {
                $csvLoader = new CsvLoader($modelContext, $disk, $options->loaders['csv'] ?? []);
                $loaderDelegate->delegateTo($csvLoader);

                try {
                    $mapper = $csvLoader->columnMapping();
                } catch (IngesterException $e) {
                    $modelContext->unusableBecause($e->modelStatusFlag);
                }

                if (isset($options->loaders['xlsx'])) {
                    $excelLoader = new ExcelLoader($modelContext, $disk, $options->loaders['excel'] ?? []);

                    $excelLoader->assertUsable();
                    $loaderDelegate->delegateTo($excelLoader);
                }

                $modelContext->uniqueItemsProvider($loaderDelegate);
            }


            $r->add($modelContext);
        }

        $r->resolveDependencies();

        return $r;
    }
}
