<?php

namespace Swark\Api\Server\Domain\Host;

use Swark\Api\Server\Internal\BaseRequest;
use Swark\Api\Server\Internal\NamingContext;
use Swark\DataModel\Business\Domain\Entity\Organization;
use Swark\DataModel\Infrastructure\Domain\Entity\Baremetal;
use Swark\DataModel\Infrastructure\Domain\Entity\Host;
use Swark\DataModel\Software\Domain\Entity\Release;
use Swark\DataModel\Software\Domain\Entity\Software;

class HostRequest extends BaseRequest
{

    public ?Release $operatingSystem = null;
    public ?Release $virtualizer = null;
    public ?Host $parentHost = null;
    public ?Baremetal $baremetal = null;
    public ?Organization $customer = null;

    public static function locateRelease(string $softwareOrRelease): ?Release
    {
        $parts = explode(":", $softwareOrRelease);
        // scomp:${software} => ${software.scomp_id}:any, ${software.scomp_id}:latest, error
        // ${software}:${release}
        // scomp:${software}:${release}
        throw_if(sizeof($parts) < 2, "invalid software discriminator. At least 2 segments required");

        $useVersion = array_pop($parts);
        $softwareRef = array_pop($parts);
        $namingType = 'scomp';

        if (sizeof($parts) > 1) {
            $namingType = $parts[0];
        }

        $software = NamingContext::ofNamedReference(Software::class, $namingType . ":" . $softwareRef)->resolve();

        $releases = $software->releases();
        $found = null;

        if ($useVersion) {
            $found = $releases->where('version', $useVersion)->get()->first();
        }

        if (!$found) {
            $found = $releases->where('is_any', true)->get()->first();
        }

        if (!$found) {
            $found = $releases->where('is_latest', true)->get()->first();
        }

        return $found;
    }

    public function rules()
    {
        return $rules = [
            'name' => 'required',
            'baremetal' => ['nullable',
                function ($attribute, $namedReference, $fail) {
                    try {
                        $this->baremetal = NamingContext::ofNamedReference(Baremetal::class, $namedReference)->resolve();
                    } catch (\Exception $e) {
                        return $fail("Named baremetal system '$namedReference' not found");
                    }
                }
            ],
            'operating_system' => ['required', function ($attribute, $namedReference, $fail) {

                try {
                    $this->operatingSystem = static::locateRelease($namedReference);

                } catch (\Exception $e) {
                    return $fail("Unable to find {$e->getMessage()} software and at least one release for $namedReference");
                }
            }],
        ];
    }
}
