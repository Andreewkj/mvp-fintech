<?php

namespace App\Domain\Requests;

use App\Domain\VO\Cnpj;
use App\Domain\VO\Cpf;
use App\Domain\VO\Email;
use App\Domain\VO\Password;
use App\Domain\Interfaces\RequestValidateInterface;
use App\Domain\Services\UserService;

class CreateUserRequest implements RequestValidateInterface
{
    private UserService $userService;

    public function __construct(
        private array $data
    ) {
        $this->userService = new UserService();
        $this->validate();
    }

    public function validate(): array
    {
        if (empty($this->data['email'])) {
            throw new \InvalidArgumentException('Email is required');
        }

        if (empty($this->data['full_name'])) {
            throw new \InvalidArgumentException('Full name is required');
        }

        if (empty($this->data['cpf']) && empty($this->data['cnpj'])) {
            throw new \InvalidArgumentException('Cpf or Cnpj is required');
        }

        if (!empty($this->data['cnpj'])) {
            $this->data['cnpj'] = (new Cnpj($this->data['cnpj']))->getValue();

            if ($this->userService->findUserByCnpj($this->data['cnpj']) !== null) {
                throw new \InvalidArgumentException('User already registered');
            }

            $this->data['cpf'] = null;

        }

        if (!empty($this->data['cpf'])) {
            $this->data['cpf'] = (new Cpf($this->data['cpf']))->getValue();

            if ($this->userService->findUserByCpf($this->data['cpf']) !== null) {
                throw new \InvalidArgumentException('User already registered');
            }

            $this->data['cnpj'] = null;
        }

        $this->data['email'] = (new Email($this->data['email']))->getValue();

        if ($this->userService->findUserByEmail($this->data['email']) !== null) {
            throw new \InvalidArgumentException('User already registered');
        }

        if (empty($this->data['password'])) {
            throw new \InvalidArgumentException('Password is required');
        }

        $this->data['password'] = (new Password($this->data['password']))->getValue();

        return $this->data;
    }
}
