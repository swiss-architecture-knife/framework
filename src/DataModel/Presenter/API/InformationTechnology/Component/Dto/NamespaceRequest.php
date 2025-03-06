<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto;

use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Presenter\API\BaseRequest;
use Swark\DataModel\Presenter\API\NamingContext;

class NamespaceRequest extends BaseRequest
{
    public ?Cluster $cluster = null;

    public function rules()
    {
        return $rules = [
            'name' => 'required',
            'cluster' => ['nullable',
                function ($attribute, $namedReference, $fail) {
                    try {
                        $this->cluster = NamingContext::ofNamedReference(Cluster::class, $namedReference)->resolve();
                    } catch (\Exception $e) {
                        return $fail("Named cluster '$namedReference' not found");
                    }
                }
            ],
        ];
    }
}
