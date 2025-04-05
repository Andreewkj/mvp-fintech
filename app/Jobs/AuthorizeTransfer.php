<?php

namespace App\Jobs;

use App\Domain\Interfaces\BankAdapterInterface;
use App\Domain\Services\TransferService;
use App\Models\Transfer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AuthorizeTransfer implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Transfer $transfer,
        private BankAdapterInterface $bankAdapter
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->bankAdapter->authorizeTransfer($this->transfer);
        } catch (\Throwable $e) {
            if ($this->attempts() >= $this->tries) {
                Log::Critical("Error authorizing transfer from user: {$this->transfer->payer_id} to user: {$this->transfer->payee_id} with value: {$this->transfer->amount}, error: {$e->getMessage()}");
                (new TransferService())->refundTransfer($this->transfer);
            }
            throw $e;
        }
    }
}
