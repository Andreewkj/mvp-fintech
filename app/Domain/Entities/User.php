<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\VO\Cpf;
use App\Domain\VO\Cnpj;
use App\Domain\VO\Email;
use App\Domain\VO\Phone;
use App\Enums\WalletTypeEnum;
use App\Exceptions\TransferException;
use DomainException;

class User
{
    public function __construct(
        private string $id,
        private string $name,
        private ?Cpf $cpf,
        private ?Cnpj $cnpj,
        private Email $email,
        private Phone $phone,
        private ?Wallet $wallet = null,
    ) {}

    public function assignWallet(Wallet $wallet): void
    {
        if ($this->wallet !== null) {
            throw new DomainException("UserModel already has a wallet");
        }

        $this->wallet = $wallet;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
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

    /**
     * @throws TransferException
     */
    public function validateTransfer(int $value): void
    {
        if ($this->wallet === null) {
            throw new TransferException('User has no wallet assigned');
        }

        $this->wallet->validateTransfer($value);
    }
}
