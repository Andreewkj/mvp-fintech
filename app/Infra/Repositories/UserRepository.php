<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Infra\Mappers\UserMapper;
use App\Models\UserModel;
use App\Domain\Entities\User;

readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(private UserModel $userModel)
    {}

    /**
     * @param User $userEntity
     * @param string $password
     * @return User
     */
    public function create(User $userEntity, string $password): User
    {
        $userModel = UserMapper::toModel($userEntity, $password);
        $userModel->save();

        return UserMapper::toEntity($userModel);
    }

    /**
     * @param string $userId
     * @param string $walletId
     * @return void
     */
    public function updateUserWallet(string $userId, string $walletId): void
    {
        $this->userModel->query()->where('id', $userId)->update(['wallet_id' => $walletId]);
    }

    /**
     * @param string $walletId
     * @return User|null
     */
    public function findUserByWalletId(string $walletId): ?User
    {
        $model = $this->userModel->query()->where('wallet_id', $walletId)->first();
        return $model ? UserMapper::toEntity($model) : null;
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email): ?User
    {
        $model = $this->userModel->query()->where('email', $email)->first();
        return $model ? UserMapper::toEntity($model) : null;
    }

    /**
     * @param string $cpf
     * @return User|null
     */
    public function findUserByCpf(string $cpf): ?User
    {
        $model = $this->userModel->query()->where('cpf', $cpf)->first();
        return $model ? UserMapper::toEntity($model) : null;
    }

    /**
     * @param string $cnpj
     * @return User|null
     */
    public function findUserByCnpj(string $cnpj): ?User
    {
        $model = $this->userModel->query()->where('cnpj', $cnpj)->first();
        return $model ? UserMapper::toEntity($model) : null;
    }

    /**
     * @param string $userId
     * @return User|null
     */
    public function findUserById(string $userId): ?User
    {
        $model = $this->userModel->query()->where('id', $userId)->first();
        return $model ? UserMapper::toEntity($model) : null;
    }
}
