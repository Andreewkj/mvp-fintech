<?php

namespace App\Application\Services;

use App\Domain\Contracts\Adapters\NotifyAdapterInterface;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;

class NotifyUserService
{
    protected NotifyAdapterInterface $notifyAdapter;
    protected UserRepositoryInterface $userRepository;

    public function __construct(
        NotifyAdapterInterface $notifyAdapter,
        UserRepositoryInterface $userRepository
    ) {
        $this->notifyAdapter = $notifyAdapter;
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $data
     */
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
