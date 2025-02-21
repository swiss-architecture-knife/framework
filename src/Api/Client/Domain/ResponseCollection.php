<?php

namespace Swark\Api\Client\Domain;

class ResponseCollection
{
    private array $items = [];

    public function __construct(public readonly JsonDataResponse $response)
    {
    }

    public function data(): array
    {
        return $this->response->data();
    }

    public function links(): array
    {
        return $this->response->data();
    }

    public function items(): array
    {
        return $this->items;
    }

    protected function setItems(array $items): ResponseCollection
    {
        $this->items = $items;
        return $this;
    }
}
