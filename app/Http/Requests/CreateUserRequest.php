<?php

namespace App\Http\Requests;

use App\Application\Services\UserService;
use App\Domain\Contracts\RequestValidateInterface;
use App\Domain\VO\Cnpj;
use App\Domain\VO\Cpf;
use App\Domain\VO\Email;
use App\Domain\VO\Password;
use App\Domain\VO\Phone;
use InvalidArgumentException;

class CreateUserRequest implements RequestValidateInterface
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function validate(array $data): array
    {
        if (empty($data['email'])) {
            throw new InvalidArgumentException('Email is required');
        }

        $data['email'] = (new Email($data['email']))->getValue();

        if ($this->userService->findUserByEmail($data['email']) !== null) {
            throw new InvalidArgumentException('UserModel already registered');
        }

        if (empty($data['full_name'])) {
            throw new InvalidArgumentException('Full name is required');
        }

        if (empty($data['cpf']) && empty($data['cnpj'])) {
            throw new InvalidArgumentException('Cpf or Cnpj is required');
        }

        if (empty($data['phone'])) {
            throw new InvalidArgumentException('phone is required');
        }

        $data['phone'] = (new Phone($data['phone']))->getValue();


        if (empty($data['password'])) {
            throw new InvalidArgumentException('Password is required');
        }

        $data['password'] = (new Password($data['password']))->getValue();

        if (!empty($data['cnpj'])) {
            $data['cnpj'] = (new Cnpj($data['cnpj']))->getValue();

            if ($this->userService->findUserByCnpj($data['cnpj']) !== null) {
                throw new InvalidArgumentException('UserModel already registered');
            }

            $data['cpf'] = null;
        }

        if (!empty($data['cpf'])) {
            $data['cpf'] = (new Cpf($data['cpf']))->getValue();

            if ($this->userService->findUserByCpf($data['cpf']) !== null) {
                throw new InvalidArgumentException('UserModel already registered');
            }

            $data['cnpj'] = null;
        }

        return $data;
    }
}
