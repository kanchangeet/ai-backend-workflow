<?php

namespace App\Application\UseCases\Auth;

use App\Application\DTOs\Auth\LoginDTO;
use App\Domain\Auth\Entities\User;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Services\AuthDomainService;
use Illuminate\Validation\ValidationException;

class LoginUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly AuthDomainService $authDomainService,
    ) {}

    public function execute(LoginDTO $dto): User
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (! $user || ! $this->authDomainService->verifyPassword($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user;
    }
}
