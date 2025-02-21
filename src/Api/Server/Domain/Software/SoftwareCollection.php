<?php
namespace Swark\Api\Server\Domain\Software;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SoftwareCollection extends ResourceCollection {

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'links' => [
                'self' => '__',
            ],
        ];
    }
}
