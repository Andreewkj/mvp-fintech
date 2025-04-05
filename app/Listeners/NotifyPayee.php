<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Domain\Services\UserService;
use App\Events\TransferWasCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifyPayee
{
    use InteractsWithQueue;

    public int $tries = 3;

    public int $backoff = 5;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TransferWasCompleted $event): void
    {
        $payee = (new UserService())->findUserById($event->transfer->payee_id);
        $event->notifyAdapter->notifyByEmail($payee);
        $event->notifyAdapter->notifyBySms($payee);
    }

    public function failed($exception): void
    {
        Log::error('Error notifying payee: ' . $exception->getMessage());
    }
}
