<?php

namespace App\Domain\Contracts\Repositories;

use App\Domain\Entities\Transfer;

interface TransferRepositoryInterface
{
    public function create(Transfer $transfer): ?Transfer;
    public function updateToDeniedStatus(Transfer $transfer): void;

    public function updateToAuthorizedStatus(Transfer $transfer): void;
}
