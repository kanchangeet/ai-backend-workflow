<?php

namespace App\Application\UseCases\Auth;

use App\Application\DTOs\Auth\RegisterDTO;
use App\Domain\Auth\Entities\User;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Services\AuthDomainService;

class RegisterUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly AuthDomainService $authDomainService,
    ) {}

    /**
     * @return array{user: User, token: string}
     */
    public function execute(RegisterDTO $dto): array
    {
        $this->authDomainService->ensureEmailIsUnique($dto->email);

        $user = User::register(
            name: $dto->name,
            email: $dto->email,
            hashedPassword: $this->authDomainService->hashPassword($dto->password),
        );

        $savedUser = $this->userRepository->save($user);

        // Token generation requires the Eloquent model — delegated to the controller
        return ['user' => $savedUser];
    }
}
