<?php

namespace App\Services;

use App\Clients\BackendClient;

class MasterService
{
    public function __construct(
        private readonly BackendClient $client,
    ) {}

    public function categories(string $token): array
    {
        return $this->client->get('/api/master/categories', token: $token);
    }

    public function list(string $token, array $params = []): array
    {
        return $this->client->get('/api/master/categories', $params, $token);
    }

    public function get(string $token, int $id): array
    {
        return $this->client->get("/api/master/categories/{$id}", token: $token);
    }

    public function create(string $token, array $data): array
    {
        return $this->client->post('/api/master/categories', $this->toBackendPayload($data), $token);
    }

    public function update(string $token, int $id, array $data): array
    {
        return $this->client->put("/api/master/categories/{$id}", $this->toBackendPayload($data), $token);
    }

    public function delete(string $token, int $id): array
    {
        return $this->client->delete("/api/master/categories/{$id}", $token);
    }

    /**
     * Map frontend 'code' field to backend 'title' field.
     */
    private function toBackendPayload(array $data): array
    {
        if (array_key_exists('code', $data)) {
            $data['title'] = $data['code'];
            unset($data['code']);
        }

        return $data;
    }
}
