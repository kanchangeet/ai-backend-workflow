<?php

namespace App\Services;

use App\Clients\BackendClient;

class AuthService
{
    public function __construct(
        private readonly BackendClient $client,
    ) {}

    public function login(string $email, string $password): array
    {
        return $this->client->post('/api/auth/login', [
            'email'    => $email,
            'password' => $password,
        ]);
    }

    public function register(string $name, string $email, string $password, string $passwordConfirmation): array
    {
        return $this->client->post('/api/auth/register', [
            'name'                  => $name,
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $passwordConfirmation,
        ]);
    }

    public function logout(string $token): array
    {
        return $this->client->post('/api/auth/logout', token: $token);
    }
}
