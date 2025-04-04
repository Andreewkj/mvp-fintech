<?php

namespace App\Domain\Services;

use App\Domain\Entities\Cnpj;
use App\Domain\Entities\Cpf;
use App\Domain\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

        return $this->userRepository->create($data);
    }

    public function findUserById(string $payee): User
    {
        return User::find($payee);
    }
}
