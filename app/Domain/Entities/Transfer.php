<?php

namespace App\Domain\Entities;

use App\Domain\VO\TransferValue;
use DateTime;

class Transfer
{
    public function __construct(
        private string $transferId,
        private string $payerWalletId,
        private string $payeeWalletId,
        private string $status,
        private TransferValue $value,
        private ?DateTime $authorizedAt,
        private ?DateTime $deniedAt,
    ) {}

    public function getTransferId(): string
    {
        return $this->transferId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
    public function getPayerWalletId(): string
    {
        return $this->payerWalletId;
    }

    public function getPayeeWalletId(): string
    {
        return $this->payeeWalletId;
    }

    public function getValue(): int
    {
        return $this->value->getValue();
    }

    public function getAuthorizedAt(): ?String
    {
        return $this->authorizedAt?->format('Y-m-d H:i:s');
    }

    public function getDeniedAt(): ?String
    {
        return $this->deniedAt?->format('Y-m-d H:i:s');
    }
}
