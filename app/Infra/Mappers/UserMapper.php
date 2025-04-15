<?php

declare(strict_types=1);

namespace App\Infra\Mappers;

use App\Domain\Entities\User;
use App\Domain\VO\Cpf;
use App\Domain\VO\Cnpj;
use App\Domain\VO\Phone;
use App\Models\UserModel;

class UserMapper
{
    public static function toEntity(UserModel $model): User
    {
        return new User(
            id: $model->id,
            name: $model->full_name,
            cpf: $model->cpf ? new Cpf($model->cpf) : null,
            cnpj: $model->cnpj ? new Cnpj($model->cnpj) : null,
            email: $model->email,
            phone: new Phone($model->phone),
            wallet: $model->wallet ? WalletMapper::toEntity($model->wallet) : null
        );
    }

    public static function toModel(User $entity, ?UserModel $model = null): UserModel
    {
        $model = $model ?: new UserModel();
        $model->id = $entity->getId();
        $model->full_name = $entity->getName();
        $model->cpf = $entity->getCpf()?->getValue();
        $model->cnpj = $entity->getCnpj()?->getValue();
        $model->email = $entity->getEmail()->getValue();
        $model->phone = $entity->getPhone()->getValue();

        return $model;
    }
}
