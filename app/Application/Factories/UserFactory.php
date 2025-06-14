<?php

namespace App\Application\Factories;

use App\Application\DTO\User\CreateUserDTO;
use App\Domain\Entities\User;
use App\Domain\VO\Cnpj;
use App\Domain\VO\Cpf;
use App\Domain\VO\Email;
use App\Domain\VO\EntityId;
use App\Domain\VO\Phone;

class UserFactory
{
    public function fromDto(CreateUserDTO $dto): User
    {
        return new User(
            userId: (new EntityId())->getValue(),
            name: $dto->fullName,
            cpf: $dto->cpf ? new Cpf($dto->cpf) : null,
            cnpj: $dto->cnpj ? new Cnpj($dto->cnpj) : null,
            email: new Email($dto->email),
            phone: new Phone($dto->phone),
        );
    }
}
