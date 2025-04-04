<?php

namespace App\Domain\Services;

use App\Domain\Repositories\WalletRepository;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Log;

class WalletService
{
    protected WalletRepository $walletRepository;

    public function __construct()
    {
        $this->walletRepository = new WalletRepository();
    }

    public function makeTransfer(User $payee, User $payer, int $value): void
    {
        $this->updatePayeeWallet($payee, $value);
        $this->updatePayerWallet($payer, $value);

        (new TransferService())->register([
            'payee_id'  => $payee->id,
            'payer_id'  => $payer->id,
            'amount'    => $value
        ]);
    }

    private function updatePayeeWallet(User $payee, int $value): void
    {
        // Preciso validar em caso de erro
        $this->walletRepository->updatePayeeWallet($payee, $value);
    }

    private function updatePayerWallet(User $payer, int $value): void
    {
        // Preciso validar em caso de erro
        $this->walletRepository->updatePayerWallet($payer, $value);
    }

    public function createWallet(array $data) : Wallet
    {
        return $this->walletRepository->create($data);
    }
}
