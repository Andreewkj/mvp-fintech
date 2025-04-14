<?php

namespace App\Domain\Interfaces\Repositories;

use App\Models\Transfer;

interface TransferRepositoryInterface
{
    public function register(array $array): ?Transfer;
    public function update(Transfer $transfer): bool;

}
