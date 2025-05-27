<?php

namespace App\Listeners;

use App\Domain\Contracts\LoggerInterface;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Events\TransferWasCompleted;
use App\Infra\Messaging\MessageBusPublisher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

class NotifyPayee implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    public function __construct(
        private readonly MessageBusPublisher $messageBusPublisher,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(TransferWasCompleted $event): void
    {
        $userRepository = app(UserRepositoryInterface::class);

        $payee = $userRepository->findUserByWalletId($event->transfer->getPayeeWalletId());

        if (is_null($payee)) {
            $this->logger->warning("Payee ID not found on transfer id: {$event->transfer->getTransferId()}");
            return;
        }

        try {
            $this->messageBusPublisher->publishMessage($payee);
        } catch (Throwable $exception) {
            $this->logger->error("Failed to notify payee ID {$payee->getUserId()}: {$exception->getMessage()}");
        }
    }

    public function failed(TransferWasCompleted $event, Throwable $exception): void
    {
        $this->logger->error("Error sending notification to payee wallet id: {$event->transfer->getPayeeWalletId()}, error: {$exception->getMessage()}");
        //maybe store notification on some queue or db to try again later
    }
}
