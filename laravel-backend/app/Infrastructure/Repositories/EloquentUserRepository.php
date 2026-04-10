<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Auth\Entities\User as UserEntity;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Models\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?UserEntity
    {
        $model = User::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByEmail(string $email): ?UserEntity
    {
        $model = User::where('email', $email)->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function save(UserEntity $user): UserEntity
    {
        $model = $user->id
            ? User::findOrFail($user->id)
            : new User();

        $model->fill([
            'name'     => $user->name,
            'email'    => $user->email,
            'password' => $user->password,
        ])->save();

        return $this->toDomain($model);
    }

    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    private function toDomain(User $model): UserEntity
    {
        return new UserEntity(
            id: $model->id,
            name: $model->name,
            email: $model->email,
            password: $model->password,
        );
    }
}
