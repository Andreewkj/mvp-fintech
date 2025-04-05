<?php

namespace App\Domain\Services;

use App\Domain\Adapters\PicPayAdapter;
use App\Domain\Repositories\TransferRepository;
use App\Enums\WalletTypeEnum;
use App\Models\Transfer;
use App\Models\Wallet;
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

    public function transfer(array $data): void
    {
        $payeeWallet = $this->walletService->findWalletByUserId($data['payee_id']);
        $payerWallet = $this->walletService->findWalletByUserId(auth()->user()->id);
        $value = filter_var($data['value'], FILTER_VALIDATE_INT);
        $value = $value ?? 0;

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
            $this->walletService->chargebackPayeeAmount($transfer->payee_id, $transfer->amount);
            $this->walletService->chargebackPayerAmount($transfer->payer_id, $transfer->amount);
            $this->updateTransferToRefund($transfer);
            // verificar logica da notificação
        } catch (\Throwable $e) {
            Log::Critical("Error rolling back transfer from user: {$transfer->payer_id} to user: {$transfer->payee_id} with value: {$transfer->amount}, error: {$e->getMessage()}");
            throw $e;
        }
    }
}
