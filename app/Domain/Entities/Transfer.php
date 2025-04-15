<?php

namespace App\Domain\Entities;

use DateTime;
use PhpParser\Node\Scalar\String_;

class Transfer
{
    public function __construct(
        private string $id,
        private string $payer_wallet_id,
        private string $payee_wallet_id,
        private string $status,
        private int $value,
        private ?DateTime $authorized_at,
        private ?DateTime $denied_at,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
    public function getPayerWalletId(): string
    {
        return $this->payer_wallet_id;
    }

    public function getPayeeWalletId(): string
    {
        return $this->payee_wallet_id;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getAuthorizedAt(): ?String
    {
        return $this->authorized_at?->format('Y-m-d H:i:s');
    }

    public function getDeniedAt(): ?String
    {
        return $this->denied_at?->format('Y-m-d H:i:s');
    }
}
