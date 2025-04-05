<?php

namespace App\Domain\Services;

use App\Domain\Entities\Account;
use App\Domain\Interfaces\BankAdapterInterface;
use App\Domain\Repositories\WalletRepository;
use App\Jobs\AuthorizeTransfer;
use App\Models\Wallet;

class WalletService
{
    protected WalletRepository $walletRepository;

    public function __construct(private readonly BankAdapterInterface $bankAdapter)
    {
        $this->walletRepository = new WalletRepository();
    }

    public function transferBetweenWallets(Wallet $payeeWallet, Wallet $payerWallet, int $value): void
    {
        $userService = new UserService();

        $this->updatePayeeWallet($payeeWallet, $value);
        $this->updatePayerWallet($payerWallet, $value);

        $transfer = (new TransferService())->register([
            'payee_id'  => $userService->findUserByWalletId($payeeWallet->id)->id,
            'payer_id'  => $userService->findUserByWalletId($payerWallet->id)->id,
            'amount'    => $value
        ]);

        AuthorizeTransfer::dispatch($transfer, $this->bankAdapter);
    }

    private function updatePayeeWallet(Wallet $payeeWallet, int $value): void
    {
        // Preciso validar em caso de erro
        $this->walletRepository->updatePayeeWallet($payeeWallet, $value);
    }

    private function updatePayerWallet(Wallet $payerWallet, int $value): void
    {
        // Preciso validar em caso de erro
        $this->walletRepository->updatePayerWallet($payerWallet, $value);
    }

    public function createWallet(array $data) : Wallet
    {
        $data['account'] = (new Account())->getValue();
        //Here probably should be post to bank and get account

        $this->validate($data);
        $wallet = $this->walletRepository->create($data);

        (new UserService())->updateUserWallet($data['user_id'], $wallet->id);

        return $wallet;
    }

    public function findWalletByUserId(string $id) : Wallet
    {
        return $this->walletRepository->findWalletByUserId($id);
    }

    private function validate(array $data): void
    {
        //Todo: validar classe de erro
        if ($this->walletRepository->userWalletExist($data['user_id'])) {
            throw new \Exception('Wallet already exists');
        }
    }

    public function chargebackPayeeAmount(string $payeeId, int $amount): void
    {
        $this->walletRepository->chargebackPayeeAmount($payeeId, $amount);
    }

    public function chargebackPayerAmount(string $payerId, int $amount): void
    {
        $this->walletRepository->chargebackPayerAmount($payerId, $amount);
    }
}
