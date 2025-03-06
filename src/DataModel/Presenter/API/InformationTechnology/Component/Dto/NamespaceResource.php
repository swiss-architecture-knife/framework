<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Swark\DataModel\Presenter\API\ModelResourceResponse;

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
