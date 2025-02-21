<?php

namespace Swark\DataModel\Kernel\Infrastructure\Repository;

class GroupByTemplate
{
    private ?GroupByTemplate $nestedTreeTemplate = null;
    private ?GroupByTemplate $parent = null;

    private function __construct(public readonly string $groupName,
                                 public readonly ?array $additionalProperties = null,
                                 public readonly bool   $hideGroup = false,
                                 public readonly bool   $uniqueCounter = true,
    )
    {
    }

    public function nest(string $groupName,
                         ?array $additionalProperties = null,
                         bool   $hideGroup = false,
                         bool   $uniqueCounter = true,
    ): GroupByTemplate
    {
        $this->nestedTreeTemplate = static::of($groupName, $additionalProperties, $hideGroup, $uniqueCounter);
        $this->nestedTreeTemplate->parent = $this;

        return $this->nestedTreeTemplate;
    }

    public function child(): ?GroupByTemplate
    {
        return $this->nestedTreeTemplate;
    }

    public function parent(): ?GroupByTemplate
    {
        return $this->parent;
    }

    public function hasChild(): bool
    {
        return $this->nestedTreeTemplate !== null;
    }

    public function root(): GroupByTemplate
    {
        return $this->parent ? $this->parent->root() : $this;
    }

    public function toGroupBy(): GroupBy
    {
        return GroupBy::of($this->root());
    }

    public static function of(string $propertyName,
                              ?array $additionalProperties = null,
                              bool   $hideGroup = false,
                              bool   $uniqueCounter = true,
    ): GroupByTemplate
    {
        return new static($propertyName, $additionalProperties, $hideGroup, $uniqueCounter);
    }
}
