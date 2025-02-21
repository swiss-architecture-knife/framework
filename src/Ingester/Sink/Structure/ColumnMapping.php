<?php
declare(strict_types=1);

namespace Swark\Ingester\Sink\Structure;

use Swark\Ingester\IngesterException;
use Swark\Ingester\Model\Relationship\Attribute;
use Swark\Ingester\Model\Relationship\Attributes;
use Swark\Ingester\Model\StatusFlag;

class ColumnMapping
{
    private array $attributesToColumns = [];

    public function __construct(public readonly Attributes $attributes)
    {
    }

    public function getColumnByAttribute(Attribute $attribute): ?Column
    {
        return $this->attributesToColumns[$attribute->name] ?? null;
    }

    public function map(string $columnName, string $attributeName): Column
    {
        $attribute = $this->attributes->get($attributeName);

        throw_if(!$attribute, new IngesterException(StatusFlag::COLUMN_MAPPER_INVALID, "Column name $columnName mapped to invalid or unknown attribute $attributeName"));

        if ($alreadyMapped = ($this->attributesToColumns[$attributeName] ?? null)) {
            throw_if($alreadyMapped->name == $attributeName, new IngesterException(StatusFlag::COLUMN_MAPPER_INVALID, "Column $columnName is already mapped to " . $alreadyMapped->name));
        } else {
            $this->attributesToColumns[$attributeName] = new Column($columnName, $attribute);
        }

        return $this->attributesToColumns[$attributeName];
    }
}
