<?php

namespace App\DTOs;

final class UserDTO
{
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly string $email,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id:    $data['id'],
            name:  $data['name'],
            email: $data['email'],
        );
    }

    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
        ];
    }
}
