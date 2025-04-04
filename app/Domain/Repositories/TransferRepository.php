<?php

namespace App\Domain\Repositories;

use App\Models\Transfer;

class TransferRepository
{
    public function register(array $array): void
    {
        // TODO: verificar horaio registrado
        Transfer::create($array);
    }
}
