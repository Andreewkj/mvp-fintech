<?php

declare(strict_types=1);

namespace App\Infra\Mappers;

use App\Domain\Entities\User;
use App\Domain\VO\Cpf;
use App\Domain\VO\Cnpj;
use App\Domain\VO\Email;
use App\Domain\VO\Phone;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;

class UserMapper
{
    /**
     * @param UserModel $model
     * @return User
     */
    public static function toEntity(UserModel $model): User
    {
        return new User(
            id: $model->id,
            name: $model->full_name,
            cpf: $model->cpf ? new Cpf($model->cpf) : null,
            cnpj: $model->cnpj ? new Cnpj($model->cnpj) : null,
            email: new Email($model->email),
            phone: new Phone($model->phone),
            wallet: $model->wallet ? WalletMapper::toEntity($model->wallet) : null
        );
    }

    /**
     * @param User $entity
     * @param string|null $password
     * @return UserModel
     */
    public static function toModel(User $entity, ?string $password): UserModel
    {
        $model = new UserModel();
        $model->id = $entity->getUserId();
        $model->full_name = $entity->getName();
        $model->cpf = $entity->getCpf()?->getValue();
        $model->cnpj = $entity->getCnpj()?->getValue();
        $model->email = $entity->getEmail()->getValue();
        $model->phone = $entity->getPhone()->getValue();

        if ($password) {
            $model->password = Hash::make($password);
        }

        return $model;
    }
}
