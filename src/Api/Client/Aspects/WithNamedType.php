<?php

namespace Swark\Api\Client\Aspects;

trait WithNamedType
{
    private array $namings = [];

    public function hasName(string $type, string $name): mixed
    {
        $this->namings[$type] = $name;
        return $this;
    }

    public function joinNamings(): array
    {
        return ['_namings' => $this->toArrayOrNull()];
    }

    public function toArrayOrNull(): ?array
    {
        if (!empty($this->namings)) {
            return collect($this->namings)->flatMap(fn($item, $type) => [$type . ":" . $item])->toArray();
        }

        return null;
    }
}
