<?php
declare(strict_types=1);

namespace Swark\Ingester;

use App\Models\Application;
use Swark\Ingester\Model\Context;
use Swark\Ingester\Model\Models;
use Swark\Ingester\Model\Relationship\Attribute;
use Swark\Ingester\Sink\Item;

class UnitOfWork
{
    /**
     * @param Models[] $models
     * @return void
     */
    public function __construct(public readonly Models $models)
    {
    }

    public function ingestAll()
    {
        foreach ($this->models as $context) {
            $this->ingest($context);
        }
    }

    private array $cache = [/** alias => [$compound => $tablePrimaryKey] */];

    private function resolveForeignKeys(Context $modelContext, Item $rawItem, array $attributeToValueMapping): array
    {
        /** @var Attribute $foreignModelReference */
        foreach ($modelContext->foreignModelReferences() as $referencedAttribute) {
            $foreignModelReference = $referencedAttribute->foreignModelReference;

            $attributeToValueMapping[$referencedAttribute->name] =
                $this->cache[$foreignModelReference->referencedModelAlias][$foreignModelReference->referencedModelAttribute];
        }
        return $attributeToValueMapping;
    }

    private function convertAllAttributes(array $attributeToValueMapping, Context $modelContext): array
    {
        /** @var Attribute $attribute */
        foreach ($modelContext->attributes() as $attribute) {
            $attributeName = $attribute->name;

            $value =
                $attribute->converter()->convert($attributeToValueMapping[$attributeName], $attribute, $attributeToValueMapping, $modelContext);

            $attributeToValueMapping[$attributeName] = $value;
        }

        return $attributeToValueMapping;
    }

    public function ingest(Context $modelContext)
    {
        $clazz = $modelContext->options()->modelClazz;

        if ($clazz == Application::class) {
            return;
        }

        /** @var Item $rawItem */
        foreach ($modelContext->getItems() as $rawItem) {
            echo "Ingesting " . $rawItem->compoundIdentifier->getId() . PHP_EOL;

            $attributeToValueMapping = $this->resolveForeignKeys($modelContext, $rawItem, $rawItem->attributeToValueMapping);
            $attributeToValueMapping = $this->convertAllAttributes($attributeToValueMapping, $modelContext);

            $item = $clazz::updateOrCreate(
                $rawItem->compoundIdentifier->getMappedIds(),
                $attributeToValueMapping);

            foreach ($rawItem->compoundIdentifier->getRawIds() as $field => $value) {
                $this->cache[$modelContext->alias][$field] = $item->id;
            }
        }
    }
}
