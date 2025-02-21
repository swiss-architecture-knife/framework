<?php

namespace Swark\Api\Server\Domain\Software;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SoftwareResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'name' => $this->name,
            'id' => $this->scomp_id,
            'is_operating_system' => $this->is_operating_system,
            'is_runtime' => $this->is_runtime,
            'releases' => $this->releases
                ->map(fn($item) => [
                    'name' => $item->version,
                    'id' => $item->scomp_id,
                    'is_latest' => $item->is_latest,
                    'is_any' => $item->is_any
                ])
        ];
    }
}
