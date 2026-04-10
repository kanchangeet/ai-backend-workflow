<?php

namespace App\Domain\Master\Entities;

use App\Domain\Master\Enums\MasterStatus;

class Category
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $title,
        public readonly ?string $description,
        public readonly MasterStatus $status,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function create(
        string $name,
        string $title,
        ?string $description = null,
        MasterStatus $status = MasterStatus::Active,
    ): self {
        return new self(
            id: null,
            name: $name,
            title: $title,
            description: $description,
            status: $status,
            createdAt: null,
            updatedAt: null,
        );
    }

    public function update(
        string $name,
        string $title,
        ?string $description,
        MasterStatus $status,
    ): self {
        return new self(
            id: $this->id,
            name: $name,
            title: $title,
            description: $description,
            status: $status,
            createdAt: $this->createdAt,
            updatedAt: null,
        );
    }
}
