<?php

namespace App\Domain\Auth\Entities;

class User
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password, // hashed
    ) {}

    public static function register(string $name, string $email, string $hashedPassword): self
    {
        return new self(
            id: null,
            name: $name,
            email: $email,
            password: $hashedPassword,
        );
    }
}
