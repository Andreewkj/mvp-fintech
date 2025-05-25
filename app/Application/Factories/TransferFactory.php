<?php

namespace App\Application\Factories;

use App\Application\DTO\Transfer\CreateTransferDTO;
use App\Domain\Entities\Transfer;
use App\Domain\Enums\TransferStatusEnum;
use App\Domain\VO\EntityId;
use App\Domain\VO\TransferValue;

class TransferFactory
{
    public static function fromDto(CreateTransferDTO $createTransferDto) : Transfer
    {
        return new Transfer(
            transferId: (new EntityId())->getValue(),
            payerWalletId: $createTransferDto->payerWallet->getWalletId(),
            payeeWalletId: $createTransferDto->payeeWallet->getWalletId(),
            status: TransferStatusEnum::STATUS_PENDING->value,
            value: new TransferValue($createTransferDto->value),
            authorizedAt: null,
            deniedAt: null,
        );
    }
}
