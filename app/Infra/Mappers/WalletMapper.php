<?php

namespace App\Infra\Mappers;

use App\Domain\Entities\Wallet;
use App\Models\WalletModel;

class WalletMapper
{
    public static function toEntity(WalletModel $model): Wallet
    {
        return new Wallet(
            id: $model->id,
            user_id: $model->user_id,
            account: $model->account,
            balance: $model->balance,
            type: $model->type,
        );
    }

    public static function toModel(Wallet $entity, ?WalletModel $model = null): WalletModel
    {
        $model = $model ?: new WalletModel();
        $model->id = $entity->getId();
        $model->user_id = $entity->getUserId();
        $model->account = $entity->getAccount();
        $model->balance = $entity->getBalance();
        $model->type = $entity->getType();

        return $model;
    }
}
