<?php

namespace App\Domain\Contracts\Repositories;

use App\Domain\Entities\Transfer;

interface TransferRepositoryInterface
{
    public function register(array $array): ?Transfer;
    public function updateToDeniedStatus(Transfer $transfer): void;

    public function updateToAuthorizedStatus(Transfer $transfer): void;
}
