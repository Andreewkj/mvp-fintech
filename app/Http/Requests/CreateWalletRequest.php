<?php

namespace App\Http\Requests;

use App\Domain\Contracts\RequestValidateInterface;
use App\Enums\WalletTypeEnum;
use InvalidArgumentException;

readonly class CreateWalletRequest implements RequestValidateInterface
{
    public function validate(array $data): array
    {
        if (empty($data['user_id'])) {
            throw new InvalidArgumentException('UserModel was not found, make sure you are logged in, or contact your support team');
        }

        if (empty($data['type'])) {
            throw new InvalidArgumentException('Wallet type was not found');
        }

        match ($data['type']) {
            'common' => WalletTypeEnum::COMMON,
            'shop_keeper' => WalletTypeEnum::SHOP_KEEPER,
            default => throw new InvalidArgumentException('Wallet type was not a valid type')
        };

        return $data;
    }
}
