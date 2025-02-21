<?php

namespace Swark\Api\Server\Internal;

class ItemReference
{
    const SCOMP_TYPE = 'scomp';

    public function __construct(public readonly int|string $idOrName,
                                public readonly ?string    $type = null,
                                public readonly array      $args = [],
    )
    {
    }

    public function isDefaultScompType(): bool
    {
        return $this->type == static::SCOMP_TYPE;
    }

    public function isScompType(): bool
    {
        return !empty($this->type);
    }

    public function isCustomScompType(): bool
    {
        return $this->isScompType() && !$this->isDefaultScompType();
    }

    public static function of(int|string|ItemReference $maybeStringifiedItemReference): ItemReference
    {
        if ($maybeStringifiedItemReference instanceof ItemReference) {
            return $maybeStringifiedItemReference;
        }

        $type = null;
        $value = $maybeStringifiedItemReference;
        $args = [];

        if (is_string($maybeStringifiedItemReference)) {
            $type = static::SCOMP_TYPE;
            $parts = explode(":", $maybeStringifiedItemReference);

            if (sizeof($parts) >= 2) {
                $type = $parts[0];
                $value = $parts[1];
            }

            if (sizeof($parts) >= 3) {
                $args = array_splice($parts, 2);
            }
        }

        return new static($value, $type, $args);
    }

    public function __toString(): string
    {
        return ($this->type ? $this->type : '<internal>') . ":" . $this->idOrName;
    }
}
