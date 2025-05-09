<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Adapters;

interface NotifyAdapterInterface
{
    public function notifyByEmail(array $data);
    public function notifyBySms(array $data);
}
