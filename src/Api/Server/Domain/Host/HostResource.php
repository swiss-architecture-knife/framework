<?php

namespace Swark\Api\Server\Domain\Host;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Swark\Api\Kernel\Aspects\ModelResourceResponse;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Release;

class HostResource extends JsonResource
{
    use ModelResourceResponse;

    public function toArray(Request $request)
    {
        return [
            'name' => $this->name,
            'id' => $this->scomp_id,
            'operating_system' => static::mayUnfoldRelease($this->operatingSystem),
            'virtualizer' => static::mayUnfoldRelease($this->virtualizer),
            'parent' => null, // not yet
            'baremetal' => [
                'id' => $this->baremetal->scomp_id,
                'name' => $this->baremetal->name,
            ]
        ];
    }

    public static function mayUnfoldRelease(?Release $release): ?array
    {
        if (!$release) {
            return null;
        }

        return [
            'name' => $release->software->name,
            'id' => $release->software->scomp_id,
            'releases' => [
                [
                    'id' => $release->scomp_id,
                    'name' => $release->version,
                    'is_any' => $release->is_any,
                    'is_latest' => $release->is_latest
                ]
            ]
        ];
    }
}
