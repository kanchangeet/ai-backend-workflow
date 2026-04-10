<?php

namespace App\DTOs;

final class MasterItemDTO
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $code,
        public readonly ?string $description,
        public readonly string  $status,
        public readonly ?string $created_at,
        public readonly ?string $updated_at,
    ) {}

    /**
     * Map from backend response (which uses 'title') to frontend shape (which uses 'code').
     */
    public static function fromBackend(array $data): self
    {
        return new self(
            id:          $data['id'],
            name:        $data['name'],
            code:        $data['title'],
            description: $data['description'] ?? null,
            status:      $data['status'],
            created_at:  $data['created_at'] ?? null,
            updated_at:  $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'code'        => $this->code,
            'description' => $this->description,
            'status'      => $this->status,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
