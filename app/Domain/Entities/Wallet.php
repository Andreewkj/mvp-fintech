<?php

namespace App\Domain\Entities;

use App\Enums\WalletTypeEnum;
use App\Exceptions\TransferException;
use DomainException;

class Wallet
{
    const MINIMUM_TRANSFER_VALUE = 0;

    public function __construct(
        private string $id,
        private string $user_id,
        private string $account,
        private int $balance,
        private string $type,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function getAccount(): string
    {
        return $this->account;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function deposit(int $value): void
    {
        if ($value <= 0) {
            throw new DomainException('Deposit value must be greater than zero');
        }

        $this->balance += $value;
    }

    public function withdraw(int $value): void
    {
        if ($value <= 0) {
            throw new DomainException('Withdraw value must be greater than zero');
        }

        if ($this->balance < $value) {
            throw new DomainException('Insufficient balance');
        }

        $this->balance -= $value;
    }

    public function isShopKeeper(): bool
    {
        return $this->type == WalletTypeEnum::SHOP_KEEPER;
    }

    public function hasSufficientBalance(int $value): bool
    {
        return $this->balance >= $value;
    }


    /**
     * @throws TransferException
     */
    public function validateTransfer(int $value): void
    {
        if ($this->isShopKeeper()) {
            throw new TransferException('Shop keeper cannot make transfers');
        }

        if (!$this->hasSufficientBalance($value)) {
            throw new TransferException('Insufficient balance');
        }

        if ($value <= self::MINIMUM_TRANSFER_VALUE) {
            throw new TransferException('Value must be greater than 0');
        }
    }
}
