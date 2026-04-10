<?php

namespace App\DTOs;

final class DashboardDTO
{
    public function __construct(
        public readonly UserDTO $user,
        public readonly int     $totalCategories,
        public readonly int     $totalUsers,
    ) {}

    public function toArray(): array
    {
        return [
            'user'             => $this->user->toArray(),
            'total_categories' => $this->totalCategories,
            'total_users'      => $this->totalUsers,
        ];
    }
}
