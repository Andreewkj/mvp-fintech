<?php

namespace App\Infra\Mappers;

use App\Domain\Entities\Transfer;
use App\Domain\VO\TransferValue;
use App\Models\TransferModel;

class TransferMapper
{
    public static function toEntity(TransferModel $model): Transfer
    {
        return new Transfer(
            transferId: $model->id,
            payerWalletId: $model->payer_wallet_id,
            payeeWalletId: $model->payee_wallet_id,
            status: $model->status,
            value: new TransferValue($model->value),
            authorizedAt: $model->authorized_at,
            deniedAt: $model->denied_at,
        );
    }

    public static function toModel(Transfer $entity, ?TransferModel $model = null): TransferModel
    {
        $model = $model ?: new TransferModel();
        $model->id = $entity->getTransferId();
        $model->payer_wallet_id = $entity->getPayerWalletId();
        $model->payee_wallet_id = $entity->getPayeeWalletId();
        $model->status = $entity->getStatus();
        $model->value = $entity->getValue();
        $model->authorized_at = $entity->getAuthorizedAt();
        $model->denied_at = $entity->getDeniedAt();

        return $model;
    }
}
