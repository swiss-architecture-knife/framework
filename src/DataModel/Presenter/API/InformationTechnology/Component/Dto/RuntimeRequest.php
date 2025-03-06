<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto;

use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Host;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Release;
use Swark\DataModel\Presenter\API\BaseRequest;
use Swark\DataModel\Presenter\API\NamingContext;

class RuntimeRequest extends BaseRequest
{
    public ?Release $release = null;
    public ?Host $host = null;

    public function rules()
    {
        return $rules = [
            'name' => 'required',
            'host' => ['required',
                function ($attribute, $namedReference, $fail) {
                    try {
                        $this->host = NamingContext::ofNamedReference(Host::class, $namedReference)->resolve();
                    } catch (\Exception $e) {
                        return $fail("Named host '$namedReference' not found");
                    }
                }
            ],
            'release' => ['required', function ($attribute, $namedReference, $fail) {

                try {
                    $this->release = HostRequest::locateRelease($namedReference);

                } catch (\Exception $e) {
                    return $fail("Unable to find {$e->getMessage()} software and at least one release for $namedReference");
                }
            }],
        ];
    }
}
