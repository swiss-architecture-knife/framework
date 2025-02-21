<?php

namespace Swark\Api\Client\Domain\Host;

use Swark\Api\Client\Context;

readonly class HostApiClient
{
    public function __construct(public Context $context)
    {
    }

    public function upsert(Host $host): HostResponse
    {
        $data = $host->toArray();

        $r = $this->context->post([
            'resource' => 'hosts',
        ], $data);

        return HostResponse::of($r);
    }
}
