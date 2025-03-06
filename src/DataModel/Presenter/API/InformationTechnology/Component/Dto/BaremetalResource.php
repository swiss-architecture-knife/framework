<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Swark\DataModel\Presenter\API\ModelResourceResponse;

class BaremetalResource extends JsonResource
{
    use ModelResourceResponse;

    public function toArray(Request $request)
    {
        return [
            'name' => $this->name,
            'id' => $this->scomp_id,
            'description' => $this->description,
            'managed' => !$this->managed ? null :
                [
                    'account' => [
                        'id' => $this->managed->account->scomp_id,
                        'name' => $this->managed->account->name,
                    ],
                    'offer' => null,
                    'region' => [
                        'id' => $this->managed->availabilityZone->region->scomp_id,
                        'name' => $this->managed->availabilityZone->region->name,
                    ],
                    'availability_zone' => [
                        'id' => $this->managed->availabilityZone->scomp_id,
                        'name' => $this->managed->availabilityZone->name,
                    ]
                ],
        ];
    }
}
