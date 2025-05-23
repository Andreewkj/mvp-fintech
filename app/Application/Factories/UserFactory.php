<?php

namespace App\Application\Factories;

use App\Application\DTO\CreateUserDto;
use App\Domain\Entities\User;
use App\Domain\VO\Cnpj;
use App\Domain\VO\Cpf;
use App\Domain\VO\Email;
use App\Domain\VO\Phone;
use Illuminate\Support\Str;

class UserFactory
{
    /**
     * @param CreateUserDto $dto
     * @return User
     */
    public static function fromDto(CreateUserDto $dto): User
    {
        return new User(
            id: Str::ulid(),
            name: $dto->fullName,
            cpf: $dto->cpf ? new Cpf($dto->cpf) : null,
            cnpj: $dto->cnpj ? new Cnpj($dto->cnpj) : null,
            email: new Email($dto->email),
            phone: new Phone($dto->phone),
            wallet: null
        );
    }
}
