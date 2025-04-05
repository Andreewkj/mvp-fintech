<?php

namespace App\Domain\Requests;

use App\Domain\Interfaces\RequestValidateInterface;
use App\Enums\WalletTypeEnum;

readonly class CreateWalletRequest implements RequestValidateInterface
{
    public function __construct(
        private array $data
    ) {
        $this->validate();
    }

    public function validate(): array
    {
        if (empty($data['user_id'])) {
            throw new \InvalidArgumentException('User was not found, make sure you are logged in, or contact your support team');
        }

        if (empty($data['type'])) {
            throw new \InvalidArgumentException('Wallet type was not found');
        }

        match ($data['type']) {
            'common' => WalletTypeEnum::COMMON,
            'shop_keeper' => WalletTypeEnum::SHOP_KEEPER,
            default => throw new \InvalidArgumentException('Wallet type was not a valid type')
        };

        return $this->data;
    }
}
