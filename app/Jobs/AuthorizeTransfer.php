<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Interfaces\Adapters\BankAdapterInterface;
use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Domain\Interfaces\Repositories\WalletRepositoryInterface;
use App\Enums\TransferStatusEnum;
use App\Models\Transfer;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthorizeTransfer implements ShouldQueue
{
    use Queueable;
    use InteractsWithSockets, SerializesModels;

    public int $tries = 3;

    public int $backoff = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Transfer $transfer
    )
    {}

    /**
     * Execute the job.
     * @throws Throwable
     */
    public function handle(
        WalletRepositoryInterface $walletRepository,
        BankAdapterInterface $bankAdapter,
        TransferRepositoryInterface $transferRepository
    ): void
    {
        try {
            if ($this->transfer->status === TransferStatusEnum::STATUS_DENIED->value) {
                throw new Exception('This transfer was denied');
            }

            $bankAdapter->authorizeTransfer($this->transfer);

            $walletRepository->updatePayeeWallet($this->transfer->payee_wallet, $this->transfer->value);
            $walletRepository->updatePayerWallet($this->transfer->payerWallet, $this->transfer->value);

            NotifyPayee::dispatch($this->transfer);
        } catch (Throwable $e) {
            $adapterName = get_class($bankAdapter);
            if ($this->attempts() >= $this->tries) {
                Log::Critical("Error authorizing transfer id: {$this->transfer->id} with adapter: {$adapterName}, error: {$e->getMessage()}");
                $this->transfer->status = TransferStatusEnum::STATUS_DENIED->value;
                $transferRepository->update($this->transfer);
            }

            throw $e;
        }
    }
}
