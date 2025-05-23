<?php

namespace App\Listeners;

use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Events\TransferWasCompleted;
use App\Infra\Messaging\MessageBusPublisher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifyPayee implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    public function __construct(
        private readonly MessageBusPublisher $messageBusPublisher
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(TransferWasCompleted $event): void
    {
        $userRepository = app(UserRepositoryInterface::class);

        $payee = $userRepository->findUserByWalletId($event->transfer->getPayeeWalletId());

        if (is_null($payee)) {
            Log::warning("Payee ID not found on transfer id: {$event->transfer->getId()}");
            return;
        }

        try {
            $this->messageBusPublisher->publishMessage($payee);
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
