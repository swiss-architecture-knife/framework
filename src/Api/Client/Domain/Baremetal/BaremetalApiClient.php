<?php

namespace Swark\Api\Client\Domain\Baremetal;

use Swark\Api\Client\Context;

readonly class BaremetalApiClient
{
    public function __construct(public Context $context)
    {
    }

    public function upsert(Baremetal $baremetal): BaremetalResponse
    {
        $r = $this->context->post([
            'resource' => 'baremetals',
        ], $baremetal->toArray());

        return BaremetalResponse::of($r);
    }
}
