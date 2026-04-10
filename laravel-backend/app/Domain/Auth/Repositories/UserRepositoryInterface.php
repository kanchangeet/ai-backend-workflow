<?php

namespace App\Domain\Auth\Repositories;

use App\Domain\Auth\Entities\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function save(User $user): User;

    public function emailExists(string $email): bool;
}
