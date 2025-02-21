<?php
declare(strict_types=1);

namespace Swark\Ingester\Model\Configuration;

use Swark\Ingester\Model\Context;
use Swark\Ingester\Model\Converter\ConverterFactory;
use Swark\Ingester\Model\Converter\Converters;
use Swark\Ingester\Model\Relationship\Attribute;
use Swark\Ingester\Model\Relationship\ForeignModelReference;
use Swark\Ingester\Model\StatusFlag;

class OptionsBuilder
{
    private ?string $model = null;

    private array $options = [];

    public function model(string $value, ?string $source = null): OptionsBuilder
    {
        return $this->set('model', $value, $source);
    }

    public function dependsOn(array|string $value, ?string $source = null): OptionsBuilder
    {
        return $this->set('dependsOn', is_string($value) ? [$value] : $value, $source);
    }

    public function mapping(array $value, ?string $source = null): OptionsBuilder
    {
        return $this->set('mapping', $value, $source);
    }

    public function loaders(array $value, ?string $source = null): OptionsBuilder
    {
        return $this->set('loaders', $value, $source);
    }

    public function updatedAtColumn(string $value, ?string $source = null): OptionsBuilder
    {
        return $this->set('updatedAtColumn', $value, $source);
    }

    private function set(string $key, mixed $value, ?string $source = null)
    {
        $this->options[$key] = new OptionValueWithSource($value, $source ?? 'default');
        return $this;
    }

    private function requireOrThrow(string $key, StatusFlag $modelStatusFlag): OptionValueWithSource
    {
        if (!isset($this->options[$key])) {
            static::throwNew($modelStatusFlag, "Config key $key has not been set");
        }

        return $this->options[$key];
    }

    private static function throwNew(StatusFlag $flag, string $message, string $source = '*')
    {
        throw new ConfigurationException($flag, $message, $source);
    }

    private function readOrDefault(string $key, mixed $default = null): OptionValueWithSource
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }

        return new OptionValueWithSource($default, '*');
    }

    public static function createConverters(
        ConverterFactory $converterFactory,
        array            $section,
        ?callable        $cb = null): ?Converters
    {
        $r = null;

        if (isset($section['converters'])) {
            if (!$cb) {
                $cb = fn($classOrAlias, $options) => $converterFactory->locate($classOrAlias, $options);
            }

            $converterSection = $section['converters'];
            $r = new Converters();

            foreach ($converterSection as $classOrAlias => $options) {

                if (is_numeric($classOrAlias)) {
                    $classOrAlias = $options;
                    $options = [];
                }

                $singleConverter = $cb($classOrAlias, $options);

                if (!$singleConverter) {
                    static::throwNew(StatusFlag::COLUMN_MAPPER_INVALID, "Converter $classOrAlias does not exist");
                }

                $r->add($singleConverter);
            }
        }

        return $r;
    }

    public function build(Context $modelContext, ConverterFactory $converterFactory,): Options
    {
        $model = $this->requireOrThrow('model', StatusFlag::INVALID_MAPPED_MODEL);

        if (!class_exists($model->value)) {
            static::throwNew(StatusFlag::INVALID_MAPPED_MODEL, "Model {$model->value} does not exist", $model->source);
        }

        $attributeMappings = [];

        if ($mapping = $this->readOrDefault('mapping', [])->value) {
            foreach ($mapping as $attributeName => $attributeOptions) {
                $converter = static::createConverters($converterFactory, $attributeOptions ?? []);

                $foreignModelTypeReference = null;

                if (isset($attributeOptions['reference'])) {
                    $ref = $attributeOptions['reference'];

                    if (!isset($ref['model'])) {
                        static::throwNew(StatusFlag::REFERENCE_MAPPER_INVALID, "Property 'model' is missing for reference");
                    }

                    if (!isset($ref['attribute'])) {
                        static::throwNew(StatusFlag::REFERENCE_MAPPER_INVALID, "Property 'attribute' is missing for reference");
                    }

                    $foreignModelTypeReference = new ForeignModelReference(
                        referencedModelAlias: $ref['model'],
                        referencedModelAttribute: $ref['attribute'],
                        isOptional: $ref['optional'] ?? false,
                    );
                }

                $attribute = new Attribute(
                    $attributeName,
                    $converter,
                    isUnique: $attributeOptions['unique'] ?? false,
                    isUpdatedAt: $attributeOptions['updated_at'] ?? false,
                    isDeletedAt: $attributeOptions['deleted_at'] ?? false,
                    foreignModelReference: $foreignModelTypeReference,
                    isNullable: $attributeOptions['nullable'] ?? false,
                );

                $attributeMappings[$attributeName] = $attribute;
            }
        }

        return new Options(
            modelClazz: $model->value,
            dependsOn: $this->readOrDefault('dependsOn', [])->value,
            attributeMappings: $attributeMappings,
            loaders: $this->readOrDefault('loaders', [])->value,
        );
    }
}
