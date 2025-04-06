<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Adapters\PicPayAdapter;
use App\Domain\Interfaces\BankAdapterInterface;
use App\Domain\Repositories\WalletRepository;
use App\Domain\Services\TransferService;
use App\Domain\Services\WalletService;
use App\Enums\TransferStatusEnum;
use App\Models\Transfer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AuthorizeTransfer implements ShouldQueue
{
    use Queueable;
    use InteractsWithSockets, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    private TransferService $transferService;

    private BankAdapterInterface $bankAdapter;
    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Transfer $transfer,
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->transferService = new TransferService(
                new WalletService(new WalletRepository())
            );

            $this->bankAdapter = new PicPayAdapter();

            if ($this->transfer->status === TransferStatusEnum::STATUS_REFUND->value) {
                throw new \Exception('Transfer already refunded');
            }

            $this->bankAdapter->authorizeTransfer($this->transfer);

            NotifyPayee::dispatch($this->transfer->payee_id);
        } catch (\Throwable $e) {
            $adapterName = get_class($this->bankAdapter);
            if ($this->attempts() >= $this->tries) {
                Log::Critical("Error authorizing transfer id: {$this->transfer->id} with adapter: {$adapterName}, error: {$e->getMessage()}");
                $this->transferService->refundTransfer($this->transfer);
            }

            throw $e;
        }
    }
}
