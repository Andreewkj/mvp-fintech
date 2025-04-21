<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Entities\Transfer;
use App\Domain\Contracts\Adapters\BankAdapterInterface;
use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\Contracts\Repositories\WalletRepositoryInterface;
use App\Enums\TransferStatusEnum;
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
            if ($this->transfer->getStatus() !== TransferStatusEnum::STATUS_PENDING->value) {
                throw new Exception('This transfer is not on pending status');
            }

            $bankAdapter->authorizeTransfer($this->transfer);

            $payeeWallet = $walletRepository->findById($this->transfer->getPayeeWalletId());
            $payerWallet = $walletRepository->findById($this->transfer->getPayerWalletId());

            $payeeWallet->credit($this->transfer->getValue());
            $payerWallet->debit($this->transfer->getValue());

            $walletRepository->updateBalance($payeeWallet);
            $walletRepository->updateBalance($payerWallet);
            $transferRepository->updateToAuthorizedStatus($this->transfer);

            NotifyPayee::dispatch($this->transfer);
        } catch (Throwable $e) {
            $adapterName = get_class($bankAdapter);

            if ($this->attempts() >= $this->tries) {
                Log::Critical("Error authorizing transfer id: {$this->transfer->getId()} with adapter: {$adapterName}, error: {$e->getMessage()}");
                $transferRepository->updateToDeniedStatus($this->transfer);
            }

            throw $e;
        }
    }
}
