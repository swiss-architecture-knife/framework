<?php

namespace Swark\Api\Server\Domain\Runtime;

use Swark\Api\Server\Domain\Host\HostRequest;
use Swark\Api\Server\Internal\BaseRequest;
use Swark\Api\Server\Internal\NamingContext;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Host;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Release;

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
