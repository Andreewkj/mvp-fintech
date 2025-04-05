<?php

namespace App\Domain\Services;

use App\Domain\Entities\Cnpj;
use App\Domain\Entities\Cpf;
use App\Domain\Repositories\UserRepository;
use App\Models\User;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function createUser(array $data): User
    {
        $cpf = isset($data['cpf']) ? (new Cpf($data['cpf']))->getValue() : null;
        $cnpj = isset($data['cnpj']) ? (new Cnpj($data['cpf']))->getValue() : null;

        $data['cpf'] = $cpf;
        $data['cnpj'] = $cnpj;
        $data['email'] = strTolower($data['email']);

        return $this->userRepository->create($data);
    }

    public function updateUserWallet(string $userId, string $walletId): void
    {
        $this->userRepository->updateUserWallet($userId, $walletId);
    }

    public function findUserByWalletId(string $id): User
    {
        return $this->userRepository->findUserByWalletId($id);
    }
}
