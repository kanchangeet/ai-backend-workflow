<?php

namespace App\Clients;

use App\Exceptions\BackendException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BackendClient
{
    private string $baseUrl;
    private int    $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.backend.url'), '/');
        $this->timeout = (int) config('services.backend.timeout', 10);
    }

    // ── Public HTTP verbs ────────────────────────────────────────────────────

    public function get(string $path, array $query = [], ?string $token = null): array
    {
        return $this->send(
            fn(PendingRequest $http) => $http->get($this->url($path), $query),
            $token,
        );
    }

    public function post(string $path, array $body = [], ?string $token = null): array
    {
        return $this->send(
            fn(PendingRequest $http) => $http->post($this->url($path), $body),
            $token,
        );
    }

    public function put(string $path, array $body = [], ?string $token = null): array
    {
        return $this->send(
            fn(PendingRequest $http) => $http->put($this->url($path), $body),
            $token,
        );
    }

    public function delete(string $path, ?string $token = null): array
    {
        return $this->send(
            fn(PendingRequest $http) => $http->delete($this->url($path)),
            $token,
        );
    }

    /**
     * Run multiple GET requests concurrently via Http::pool().
     *
     * $requests = ['user' => ['/api/auth/me'], 'master' => ['/api/master/categories']]
     * Returns ['user' => [...], 'master' => [...]]
     */
    public function concurrentGet(array $requests, ?string $token = null): array
    {
        $baseUrl = $this->baseUrl;
        $timeout = $this->timeout;

        $responses = Http::pool(function (Pool $pool) use ($requests, $baseUrl, $timeout, $token) {
            foreach ($requests as $key => $path) {
                $pending = $pool->as($key)
                    ->timeout($timeout)
                    ->acceptJson()
                    ->withHeaders(['X-BFF-Source' => 'bff']);

                if ($token) {
                    $pending = $pending->withToken($token);
                }

                $pending->get($baseUrl . '/' . ltrim($path, '/'));
            }
        });

        $result = [];
        foreach ($requests as $key => $_) {
            $response = $responses[$key];
            if ($response instanceof \Throwable) {
                throw new BackendException('Backend request failed: ' . $response->getMessage(), 502);
            }
            if ($response->failed()) {
                $this->handleError($response);
            }
            $result[$key] = $response->json() ?? [];
        }

        return $result;
    }

    // ── Internal ─────────────────────────────────────────────────────────────

    private function send(callable $call, ?string $token): array
    {
        $http = $this->build($token);

        /** @var Response $response */
        $response = $call($http);

        if ($response->failed()) {
            $this->handleError($response);
        }

        return $response->json() ?? [];
    }

    private function build(?string $token): PendingRequest
    {
        $http = Http::timeout($this->timeout)
            ->acceptJson()
            ->withHeaders(['X-BFF-Source' => 'bff']);

        if ($token) {
            $http = $http->withToken($token);
        }

        return $http;
    }

    private function handleError(Response $response): never
    {
        Log::warning('Backend request failed', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        throw new BackendException(
            message: $response->json('message') ?? 'Backend service error.',
            status: $response->status(),
        );
    }

    private function url(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }
}
