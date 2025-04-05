<?php

namespace App\Jobs;

use App\Domain\Interfaces\BankAdapterInterface;
use App\Domain\Services\TransferService;
use App\Enums\TransferStatusEnum;
use App\Models\Transfer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AuthorizeTransfer
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Transfer             $transfer,
        private readonly BankAdapterInterface $bankAdapter
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
            if ($this->transfer->status === TransferStatusEnum::STATUS_REFUND->value) {
                throw new \Exception('Transfer already refunded');
            }

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
