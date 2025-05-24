<?php

namespace App\Domain\Entities;

use App\Domain\Enums\WalletTypeEnum;
use App\Domain\Exceptions\TransferException;
use DomainException;

class Wallet
{
    const MINIMUM_TRANSFER_VALUE = 0;

    public function __construct(
        private string $walletId,
        private string $userId,
        private int $balance,
        private string $type,
    ) {}

    public function getWalletId(): string
    {
        return $this->walletId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isShopKeeper(): bool
    {
        return $this->type == WalletTypeEnum::SHOP_KEEPER;
    }

    public function hasSufficientBalance(int $value): bool
    {
        return $this->balance >= $value;
    }

    public function debit(int $value): void
    {
        if ($this->balance < $value) {
            throw new DomainException('Insufficient balance');
        }

        $this->balance -= $value;
    }

    public function credit(int $value): void
    {
        $this->balance += $value;
    }

    /**
     * @param int $value
     * @param Wallet|null $payeeWallet
     * @throws TransferException
     */
    public function validateTransfer(int $value, ?Wallet $payeeWallet): void
    {
        if (!$payeeWallet) {
            throw new TransferException('Payee wallet not found');
        }

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
