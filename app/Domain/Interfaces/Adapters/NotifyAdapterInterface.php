<?php

declare(strict_types=1);

namespace App\Domain\Interfaces\Adapters;

use App\Models\UserModel;

interface NotifyAdapterInterface
{
    public function notifyByEmail(UserModel $user);
    public function notifyBySms(UserModel $user);
}
