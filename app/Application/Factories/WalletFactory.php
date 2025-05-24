<?php

namespace App\Application\Factories;

use App\Application\DTO\Wallet\CreateWalletDTO;
use App\Domain\Entities\Wallet;
use App\Domain\VO\EntityId;
use Illuminate\Support\Str;

class WalletFactory
{
    public static function fromDto(CreateWalletDTO $createWalletDto) : Wallet
    {
        return new Wallet(
            id: (new EntityId())->getValue(),
            user_id: $createWalletDto->userId,
            balance: $createWalletDto->balance,
            type: $createWalletDto->type
        );
    }
}
