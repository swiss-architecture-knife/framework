<?php

namespace Swark\Api\Client\Domain\Namespace;

use Swark\Api\Client\Context;

readonly class NamespaceApiClient
{
    public function __construct(public Context $context)
    {
    }

    public function upsert(Namespace_ $namespace): NamespaceResponse
    {
        $data = $namespace->toArray();

        $r = $this->context->post([
            'resource' => 'namespaces',
        ], $data);

        return NamespaceResponse::of($r);
    }
}
