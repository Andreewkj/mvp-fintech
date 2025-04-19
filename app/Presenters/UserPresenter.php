<?php

namespace App\Presenters;

use App\Domain\Entities\User;

class UserPresenter
{
    public static function fromEntity(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()->getValue(),
        ];
    }
}
