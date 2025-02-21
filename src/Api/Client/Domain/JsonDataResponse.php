<?php

namespace Swark\Api\Client\Domain;

class JsonDataResponse implements \ArrayAccess
{
    private array $metaStatus = [];

    private array $data = [];
    private array $links = [];

    public function __construct(
        public readonly array $raw,
        public readonly int   $status,
    )
    {
        $this->data = $raw['data'] ?? [];
        $this->links = $raw['links'] ?? [];
        $this->metaStatus = $raw['__status'] ?? [];
    }

    public function metaStatus(): ?array
    {
        return $this->metaStatus;
    }

    public function hasBeenChanged(): ?bool
    {
        return $this->metaStatus['changed'] ?? null;
    }

    public function hasBeenCreated(): ?bool
    {
        return $this->metaStatus['created'] ?? null;
    }

    public function noModification(): ?bool
    {
        return ($this->metaStatus && (false === $this->hasBeenCreated()) && (false === $this->hasBeenChanged())) || null;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function links(): array
    {
        return $this->links;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}
