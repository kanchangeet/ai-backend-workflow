<?php

namespace App\Application\DTOs\Master;

use App\Domain\Master\Enums\MasterStatus;

final class CreateCategoryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $title,
        public readonly ?string $description,
        public readonly MasterStatus $status,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            title: $data['title'],
            description: $data['description'] ?? null,
            status: MasterStatus::from($data['status'] ?? MasterStatus::Active->value),
        );
    }
}
