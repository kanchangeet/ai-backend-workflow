<?php

namespace App\Application\DTOs\Master;

use App\Domain\Master\Enums\MasterStatus;

final class UpdateCategoryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $title,
        public readonly ?string $description,
        public readonly MasterStatus $status,
    ) {}

    public static function fromArray(int $id, array $data): self
    {
        return new self(
            id: $id,
            name: $data['name'],
            title: $data['title'],
            description: $data['description'] ?? null,
            status: MasterStatus::from($data['status']),
        );
    }
}
