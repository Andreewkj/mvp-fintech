<?php

namespace App\Http\Requests;

use App\Application\DTO\Wallet\CreateWalletDTO;
use App\Domain\Contracts\CreateWalletRequestValidateInterface;
use App\Domain\Enums\WalletTypeEnum;
use InvalidArgumentException;

readonly class CreateWalletRequest implements CreateWalletRequestValidateInterface
{
    public function validate(array $data): CreateWalletDTO
    {
        $this->validateRequiredFields($data);

        match ($data['type']) {
            'common' => WalletTypeEnum::COMMON,
            'shop_keeper' => WalletTypeEnum::SHOP_KEEPER,
            default => throw new InvalidArgumentException('Wallet type was not a valid type')
        };

        $data['balance'] = 0;

        return new CreateWalletDTO(
            $data['user_id'],
            $data['type'],
            $data['balance']
        );
    }

    private function validateRequiredFields(array $data): void
    {
        if (empty($data['user_id'])) {
            throw new InvalidArgumentException('UserModel was not found, make sure you are logged in, or contact your support team');
        }

        if (empty($data['type'])) {
            throw new InvalidArgumentException('Wallet type was not found');
        }
    }
}
