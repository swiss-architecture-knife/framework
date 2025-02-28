<?php

namespace Swark\Api\Server\Domain\Namespace;

use Swark\Api\Server\Internal\BaseRequest;
use Swark\Api\Server\Internal\NamingContext;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Cluster;

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
