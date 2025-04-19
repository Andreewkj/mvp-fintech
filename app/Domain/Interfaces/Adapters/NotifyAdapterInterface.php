<?php

declare(strict_types=1);

namespace App\Domain\Interfaces\Adapters;

use App\Domain\Entities\User;

interface NotifyAdapterInterface
{
    public function notifyByEmail(User $user);
    public function notifyBySms(User $user);
}
