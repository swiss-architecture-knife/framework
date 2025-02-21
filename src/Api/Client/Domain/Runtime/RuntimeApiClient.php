<?php

namespace Swark\Api\Client\Domain\Runtime;

use Swark\Api\Client\Context;

readonly class RuntimeApiClient
{
    public function __construct(public Context $context)
    {
    }

    public function upsert(Runtime $resource): RuntimeResponse
    {
        $data = $resource->toArray();

        $r = $this->context->post([
            'resource' => 'runtimes',
        ], $data);

        return RuntimeResponse::of($r);
    }
}
