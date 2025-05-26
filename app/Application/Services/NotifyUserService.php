<?php

namespace App\Application\Services;

use App\Domain\Contracts\Adapters\NotifyAdapterInterface;

class NotifyUserService
{
    public function __construct(
        private NotifyAdapterInterface  $notifyAdapter
    ) {
    }

    public function notifyByEmail(array $data): void
    {
        $this->notifyAdapter->notifyByEmail($data);
    }

    /**
     * @param array $data
     */
    public function notifyBySms(array $data): void
    {
        $this->notifyAdapter->notifyBySms($data);
    }
}
