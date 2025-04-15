<?php

namespace App\Infra\Mappers;

use App\Domain\Entities\Transfer;
use App\Models\TransferModel;

class TransferMapper
{
    public static function toEntity(TransferModel $model): Transfer
    {
        return new Transfer(
            id: $model->id,
            payer_wallet_id: $model->payer_wallet_id,
            payee_wallet_id: $model->payee_wallet_id,
            status: $model->status,
            value: $model->value,
            authorized_at: $model->authorized_at,
            denied_at: $model->denied_at,
        );
    }

    public static function toModel(Transfer $entity, ?TransferModel $model = null): TransferModel
    {
        $model = $model ?: new TransferModel();
        $model->id = $entity->getId();
        $model->payer_wallet_id = $entity->getPayerWalletId();
        $model->payee_wallet_id = $entity->getPayeeWalletId();
        $model->status = $entity->getStatus();
        $model->value = $entity->getValue();
        $model->authorized_at = $entity->getAuthorizedAt();
        $model->denied_at = $entity->getDeniedAt();

        return $model;
    }
}
