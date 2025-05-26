<?php

declare(strict_types=1);

namespace App\Infra\Mappers;

use App\Domain\Entities\Wallet;
use App\Models\WalletModel;

class WalletMapper
{
    public static function toEntity(WalletModel $model): Wallet
    {
        return new Wallet(
            walletId: $model->id,
            userId: $model->user_id,
            balance: $model->balance,
            type: $model->type,
        );
    }

    public static function toModel(Wallet $entity, ?WalletModel $model = null): WalletModel
    {
        $model = $model ?: new WalletModel();
        $model->id = $entity->getWalletId();
        $model->user_id = $entity->getUserId();
        $model->balance = $entity->getBalance();
        $model->type = $entity->getType();

        return $model;
    }
}
