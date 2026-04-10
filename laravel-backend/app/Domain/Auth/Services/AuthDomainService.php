<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthDomainService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function ensureEmailIsUnique(string $email): void
    {
        if ($this->userRepository->emailExists($email)) {
            throw new \DomainException("The email address is already registered.");
        }
    }

    public function hashPassword(string $plainPassword): string
    {
        return Hash::make($plainPassword);
    }

    public function verifyPassword(string $plain, string $hashed): bool
    {
        return Hash::check($plain, $hashed);
    }
}
