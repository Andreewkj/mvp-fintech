<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\VO\Cnpj;
use App\Domain\VO\Cpf;
use App\Domain\VO\Email;
use App\Domain\VO\Phone;
use DomainException;

class User
{
    public function __construct(
        private string $userId,
        private string $name,
        private ?Cpf $cpf,
        private ?Cnpj $cnpj,
        private Email $email,
        private Phone $phone,
    ) {}

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPhone(): Phone
    {
        return $this->phone;
    }

    public function getCpf(): ?Cpf
    {
        return $this->cpf;
    }

    public function getCnpj(): ?Cnpj
    {
        return $this->cnpj;
    }
}
