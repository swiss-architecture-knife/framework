<?php

namespace Swark\Api\Kernel\Aspects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait ModelResourceResponse
{
    public function with(Request $request)
    {
        if (!($this->resource instanceof Model)) {
            return $this->with;
        }

        $created = $this->resource->wasRecentlyCreated;
        $changed = $this->resource->isDirty();

        return $this->with + ['__status' => [
                'created' => $created,
                'changed' => $changed,
            ]];
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        if (!($this->resource instanceof Model)) {
            return $response;
        }

        $created = $this->resource->wasRecentlyCreated;
        $changed = $this->resource->isDirty();

        return $response->setStatusCode(match (true) {
            $created => 201,
            $changed => 206,
            default => 200,
        });

    }
}
