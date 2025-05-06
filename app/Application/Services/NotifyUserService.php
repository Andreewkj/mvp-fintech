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

    public function execute(array $data): void
    {
        $payee = $this->userRepository->findUserById($data['user_id']);

        if (!$payee) {
            // talvez logar ou jogar para DLQ
            return;
        }

        //tratar o erro do provedor
        $this->notifyAdapter->notifyByEmail($payee);
        $this->notifyAdapter->notifyBySms($payee);
    }
}
