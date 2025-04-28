<?php

namespace App\Listeners;

use App\Domain\Contracts\Adapters\NotifyAdapterInterface;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Events\TransferWasCompleted;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifyPayee implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;
    public int $backoff = 2;

    /**
     * @throws Throwable
     */
    public function handle(TransferWasCompleted $event): void
    {
        $userRepository = app(UserRepositoryInterface::class);
        $notifyAdapter = app(NotifyAdapterInterface::class);

        $payee = $userRepository->findUserByWalletId($event->transfer->getPayeeWalletId());

        if (is_null($payee)) {
            Log::warning("Payee ID not found on transfer id: {$event->transfer->getId()}");
            return;
        }

        try {
            $notifyAdapter->notifyByEmail($payee);
            $notifyAdapter->notifyBySms($payee);
        } catch (Throwable $e) {
            Log::error("Failed to notify payee ID {$payee->getId()}: {$e->getMessage()}");
        }
    }

    public function failed(TransferWasCompleted $event, Throwable $e): void
    {
        Log::Error("Error sending notification to payee wallet id: {$event->transfer->getPayeeWalletId()}, error: {$e->getMessage()}");
        //maybe store notification on some queue or db to try again later
    }
}
