<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Swark\DataModel\Presenter\API\ModelResourceResponse;

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
