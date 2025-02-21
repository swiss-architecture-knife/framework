<?php

namespace Swark\Api\Client\Domain\Cluster;

use Swark\Api\Client\Context;

readonly class ClusterApiClient
{
    public function __construct(public Context $context)
    {
    }

    public function upsert(Cluster $cluster): ClusterResponse
    {
        $data = $cluster->toArray();

        $r = $this->context->post([
            'resource' => 'clusters',
        ], $data);

        return ClusterResponse::of($r);
    }
}
