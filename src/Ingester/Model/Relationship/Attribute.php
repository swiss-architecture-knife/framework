<?php
declare(strict_types=1);

namespace Swark\Ingester\Model\Relationship;

use Swark\Ingester\Model\Converter\Converter;
use Swark\Ingester\Model\Converter\PassthroughConverter;

/**
 * Configures the mapping of a model attribute
 */
class Attribute
{
    private ?Converter $converter = null;

    public function __construct(
        public readonly string                 $name,
        ?Converter                             $converter = null,
        public readonly bool                   $isUnique = false,
        public readonly bool                   $isUpdatedAt = false,
        public readonly bool                   $isDeletedAt = false,
        public readonly ?ForeignModelReference $foreignModelReference = null,
        public readonly bool                   $isNullable = false,
    )
    {
        $this->converter = $converter;
    }

    public function converter(?Converter $converter = null): Converter
    {
        if ($converter !== null) {
            $this->converter = $converter;
        }

        if (!$this->converter) {
            $this->converter = new PassthroughConverter();
        }

        return $this->converter;
    }
}
