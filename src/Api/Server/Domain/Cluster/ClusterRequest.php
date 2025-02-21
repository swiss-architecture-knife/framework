<?php

namespace Swark\Api\Server\Domain\Cluster;


use Swark\Api\Server\Internal\BaseRequest;

class ClusterRequest extends BaseRequest
{
    public function rules()
    {
        return $rules = [
            'name' => 'required',
        ];
    }
}
