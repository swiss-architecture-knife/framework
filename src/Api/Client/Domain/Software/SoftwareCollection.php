<?php

namespace Swark\Api\Client\Domain\Software;

use Swark\Api\Client\Domain\JsonDataResponse;
use Swark\Api\Client\Domain\ResponseCollection;

class SoftwareCollection extends ResponseCollection
{
    public static function of(JsonDataResponse $response): SoftwareCollection
    {
        $r = new static($response);

        $items = collect($r->data())
            ->map(fn($item) => SoftwareResponse::of($item))
            ->toArray();

        return $r->setItems($items);
    }
}
