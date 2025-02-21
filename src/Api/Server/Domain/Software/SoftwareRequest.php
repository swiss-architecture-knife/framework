<?php

namespace Swark\Api\Server\Domain\Software;

use Swark\Api\Server\Internal\BaseRequest;

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
