<?php

namespace Swark\Api\Client;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Swark\Api\Client\Domain\JsonDataResponse;

readonly class Context
{
    public function __construct(public array $defaultParameters)
    {
    }

    private function build(array $urlParameters = []): PendingRequest
    {
        $r = Http::withUrlParameters(
            $this->defaultParameters + $urlParameters
        );

        return $r;

    }

    private function handleResponse(Response $response): JsonDataResponse
    {
        $response
            ->throwIfServerError()
            ->throwIfStatus(fn($status) => $status >= 400);

        $json = $response->json();

        if (!$json) {
            // debug only
            var_dump($response->body());
            throw new \Exception("Did not receive valid JSON");
        }

        return new JsonDataResponse($json, $response->status());
    }

    public function post(array $urlParameters = [], array $args = [], ?string $customUrl = null): JsonDataResponse
    {
        $r = $this->build($urlParameters)->post($customUrl ?? '{+endpoint}/{resource}', $args);

        return $this->handleResponse($r);
    }

    public function get(array $urlParameters = [], array $args = [], ?string $customUrl = null): JsonDataResponse
    {
        $r = $this->build($urlParameters)->get($customUrl ?? '{+endpoint}/{resource}', $args);

        return $this->handleResponse($r);
    }
}
