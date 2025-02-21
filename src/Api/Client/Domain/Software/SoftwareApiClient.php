<?php

namespace Swark\Api\Client\Domain\Software;

use Swark\Api\Client\Context;

readonly class SoftwareApiClient
{
    public function __construct(public Context $context)
    {
    }

    public function find(...$args): SoftwareCollection
    {
        $r = $this->context->get([
            'resource' => 'softwares',
            ... $args
        ]);

        return SoftwareCollection::of($r);
    }

    public function upsert(Software $software): SoftwareResponse
    {
        $r = $this->context->post([
            'resource' => 'softwares',
        ], $software->toArray());

        return SoftwareResponse::of($r);
    }
}
