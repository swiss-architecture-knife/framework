<?php

namespace Swark\Api\Server\Domain\Runtime;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Swark\Api\Kernel\Aspects\ModelResourceResponse;
use Swark\Api\Server\Domain\Host\HostResource;

class RuntimeResource extends JsonResource
{
    use ModelResourceResponse;

    public function toArray(Request $request)
    {
        return [
            'name' => $this->name,
            'id' => $this->scomp_id,
            'software' => HostResource::mayUnfoldRelease($this->release),
            'host' => [
                'id' => $this->host->scomp_id,
                'name' => $this->host->name,
            ]
        ];
    }
}
