<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto;

use Swark\DataModel\Presenter\API\BaseRequest;

class SoftwareRequest extends BaseRequest
{

    public function rules()
    {
        return $rules = [
            'name' => 'required',
            'is_runtime' => ['optional'],
            'is_operating_system' => ['optional'],
        ];
    }
}
