<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\DTO\User\CreateUserDTO;
use App\Application\Services\UserService;
use App\Domain\Contracts\CreateUserRequestValidateInterface;
use App\Domain\VO\Cnpj;
use App\Domain\VO\Cpf;
use App\Domain\VO\Email;
use App\Domain\VO\Password;
use App\Domain\VO\Phone;
use InvalidArgumentException;

class CreateUserRequest implements CreateUserRequestValidateInterface
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * @param array $data
     * @return CreateUserDTO
     */
    public function validate(array $data): CreateUserDTO
    {
        if (empty($data['email'])) {
            throw new InvalidArgumentException('Email is required');
        }

        $email = (new Email($data['email']))->getValue();

        if ($this->userService->findUserByEmail($email) !== null) {
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

        $phone = (new Phone($data['phone']))->getValue();

        if (empty($data['password'])) {
            throw new InvalidArgumentException('Password is required');
        }

        $password = (new Password($data['password']))->getValue();

        $cpf = null;
        $cnpj = null;

        if (!empty($data['cnpj'])) {
            $cnpj = (new Cnpj($data['cnpj']))->getValue();
            if ($this->userService->findUserByCnpj($cnpj) !== null) {
                throw new InvalidArgumentException('UserModel already registered');
            }
        }

        if (!empty($data['cpf'])) {
            $cpf = (new Cpf($data['cpf']))->getValue();
            if ($this->userService->findUserByCpf($cpf) !== null) {
                throw new InvalidArgumentException('UserModel already registered');
            }
        }

        return new CreateUserDTO(
            email: $email,
            fullName: $data['full_name'],
            cpf: $cpf,
            cnpj: $cnpj,
            phone: $phone,
            password: $password
        );
    }
}
