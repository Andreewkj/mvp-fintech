<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Adapters\PicPayAdapter;
use App\Domain\Repositories\TransferRepository;
use App\Enums\WalletTypeEnum;
use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferService
{
    protected TransferRepository $transferRepository;
    protected WalletService $walletService;

    const MINIMUM_TRANSFER_VALUE = 0;

    public function __construct()
    {
        $this->walletService = new WalletService(new PicPayAdapter());
        $this->transferRepository = new TransferRepository();
    }

    /**
     * @throws \Exception
     */
    public function transfer(array $data): void
    {
        $payeeWallet = $this->walletService->findWalletByUserId($data['payee_id']);
        $payerWallet = $this->walletService->findWalletByUserId(auth()->user()->id);
        $value = $data['value'];

        $this->validateTransfer($payeeWallet, $payerWallet, $value);

        $this->walletService->transferBetweenWallets($payeeWallet, $payerWallet, $value);
    }

    private function validateTransfer(Wallet $payeeWallet, Wallet $payerWallet, int $amount): void
    {
        //TODO: Revisar a classe do erro
        if ($payeeWallet->type === WalletTypeEnum::SHOP_KEEPER->value) {
            throw new \InvalidArgumentException('Shop keeper cannot make transfers');
        }

        if ($amount <= self::MINIMUM_TRANSFER_VALUE) {
            throw new \InvalidArgumentException('Value must be greater than 0');
        }

        if ($payeeWallet->balance < $amount) {
            throw new \InvalidArgumentException('Insufficient balance');
        }

        if ($payeeWallet->id === $payerWallet->id) {
            throw new \InvalidArgumentException('Payee and payer cannot be the same');
        }
    }

    public function register(array $array): Transfer
    {
        return $this->transferRepository->register($array);
    }

    private function updateTransferToRefund(Transfer $transfer): void
    {
        $this->transferRepository->updateTransferToRefund($transfer);
    }

    public function refundTransfer(Transfer $transfer): void
    {
        try {
            DB::transaction(function () use ($transfer) {
                $this->walletService->chargebackPayeeAmount($transfer->payee_id, $transfer->amount);
                $this->walletService->chargebackPayerAmount($transfer->payer_id, $transfer->amount);
                $this->updateTransferToRefund($transfer);
            });

            // Might be a refund notification here
        } catch (\Throwable $e) {
            Log::Critical("Error rolling back transfer id: {$transfer->id}, error: {$e->getMessage()}");
        }
    }

    public function validateRequest(array $data): void
    {
        if (empty($data['payee_id'])) {
            throw new \InvalidArgumentException('Payee id is required');
        }

        if (empty($data['value'])) {
            throw new \InvalidArgumentException('Value is required');
        }

        if (gettype($data['value']) !== 'integer') {
            throw new \InvalidArgumentException('Value must be an integer');
        }

        if (gettype($data['payee_id']) !== 'string') {
            throw new \InvalidArgumentException('Payee id must be a string');
        }
    }
}
