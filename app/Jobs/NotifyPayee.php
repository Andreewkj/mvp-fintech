<?php

namespace App\Jobs;

use App\Domain\Entities\Transfer;
use App\Domain\Interfaces\Adapters\NotifyAdapterInterface;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifyPayee implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Transfer $transfer,
    )
    {}

    /**
     * Execute the job.
     * @throws Throwable
     */
    public function handle(
        UserRepositoryInterface $userRepository,
        NotifyAdapterInterface  $notifyAdapter
    ): void
    {
        try {
            $payee = $userRepository->findUserByWalletId($this->transfer->getPayeeWalletId());

            if (is_null($payee)) {
                throw new Exception("Payee ID not found to notify on transfer id: {$this->transfer->getId()}");
            }

            $notifyAdapter->notifyByEmail($payee);
            $notifyAdapter->notifyBySms($payee);
        } catch (Throwable $e) {
            $adapterName = get_class($notifyAdapter);
            if ($this->attempts() >= $this->tries) {
                Log::Error("Error sending notification to payee id: {$payee->getId()} with adapter: {$adapterName}, error: {$e->getMessage()}");
                //maybe store notification on some queue or db to try again later
            }

            throw $e;
        }
    }
}
