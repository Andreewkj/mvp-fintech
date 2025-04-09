<?php

declare(strict_types=1);

namespace App\Domain\Interfaces\Adapters;

use App\Models\User;

interface NotifyAdapterInterface
{
    public function notifyByEmail(User $user);
    public function notifyBySms(User $user);
}
