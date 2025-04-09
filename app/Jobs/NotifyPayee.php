<?php

namespace App\Jobs;

use App\Domain\Adapters\UltraNotifyAdapter;
use App\Domain\Interfaces\Adapters\NotifyAdapterInterface;
use App\Domain\Services\UserService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifyPayee implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 2;

    private NotifyAdapterInterface $notifyAdapter;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly String $userId
    )
    {}

    /**
     * Execute the job.
     * @throws Throwable
     */
    public function handle(): void
    {
        try {
            $this->notifyAdapter = new UltraNotifyAdapter();
            $payee = (new UserService())->findUserById($this->userId);

            if (is_null($payee)) {
                throw new Exception("Payee ID {$payee->id} not found to notify");
            }

            $this->notifyAdapter->notifyByEmail($payee);
            $this->notifyAdapter->notifyBySms($payee);
        } catch (Throwable $e) {
            $adapterName = get_class($this->notifyAdapter);
            if ($this->attempts() >= $this->tries) {
                Log::Error("Error sending notification to payee id: {$this->userId} with adapter: {$adapterName}, error: {$e->getMessage()}");
                //maybe store notification on some queue or db to try again later
            }

            throw $e;
        }
    }
}
