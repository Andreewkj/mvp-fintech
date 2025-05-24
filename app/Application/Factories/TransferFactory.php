<?php

namespace App\Application\Factories;

use App\Application\DTO\Transfer\CreateTransferDTO;
use App\Domain\Entities\Transfer;
use App\Domain\Enums\TransferStatusEnum;
use App\Domain\VO\EntityId;
use App\Domain\VO\TransferValue;
use Illuminate\Support\Str;

class TransferFactory
{

    public static function fromDto(CreateTransferDTO $createTransferDto) : Transfer
    {
        return new Transfer(
            id: (new EntityId())->getValue(),
            payer_wallet_id: $createTransferDto->payerWallet->getId(),
            payee_wallet_id: $createTransferDto->payeeWallet->getId(),
            status: TransferStatusEnum::STATUS_PENDING->value,
            value: new TransferValue($createTransferDto->value),
            authorized_at: null,
            denied_at: null,
        );
    }
}
