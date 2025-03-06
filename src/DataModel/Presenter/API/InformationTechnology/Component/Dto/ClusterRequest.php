<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto;


use Swark\DataModel\Presenter\API\BaseRequest;

class ClusterRequest extends BaseRequest
{
    public function rules()
    {
        return $rules = [
            'name' => 'required',
        ];
    }
}
