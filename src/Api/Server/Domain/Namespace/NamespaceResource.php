<?php

namespace Swark\Api\Server\Domain\Namespace;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Swark\Api\Kernel\Aspects\ModelResourceResponse;

class NamespaceResource extends JsonResource
{
    use ModelResourceResponse;

    public function toArray(Request $request)
    {
        return [
            'name' => $this->name,
            'id' => $this->scomp_id,
            'cluster' => null,
        ];
    }
}
