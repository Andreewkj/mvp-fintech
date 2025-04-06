<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\VO\Account;
use App\Domain\Interfaces\BankAdapterInterface;
use App\Domain\Repositories\WalletRepository;
use App\Exceptions\WalletException;
use App\Jobs\AuthorizeTransfer;
use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function __construct(
        private WalletRepository      $walletRepository,
        private UserService           $userService,
        private TransferService       $transferService,
        private ?BankAdapterInterface $bankAdapter = null
    )
    {}

    public function transferBetweenWallets(Wallet $payeeWallet, Wallet $payerWallet, int $value): Transfer
    {
        try {
            $transfer = null;

            DB::transaction(function () use ($payeeWallet, $payerWallet, $value, &$transfer) {
                $this->updatePayeeWallet($payeeWallet, $value);
                $this->updatePayerWallet($payerWallet, $value);

                $payeeUser = $this->userService->findUserByWalletId($payeeWallet->id);
                $payerUser = $this->userService->findUserByWalletId($payerWallet->id);

                $transfer = $this->transferService->register([
                    'payee_id' => $payeeUser->id,
                    'payer_id' => $payerUser->id,
                    'amount'   => $value
                ]);
            });

            if (is_null($transfer)) {
                throw new WalletException('Transfer could not be created');
            }

            AuthorizeTransfer::dispatch($transfer, $this->bankAdapter);

            return $transfer;
        } catch (WalletException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function updatePayeeWallet(Wallet $payeeWallet, int $value): void
    {
        $this->walletRepository->updatePayeeWallet($payeeWallet, $value);
    }

    private function updatePayerWallet(Wallet $payerWallet, int $value): void
    {
        $this->walletRepository->updatePayerWallet($payerWallet, $value);
    }

    /**
     * @throws WalletException
     */
    public function createWallet(array $data) : Wallet
    {
        $data['account'] = (new Account())->getValue();
        //Here probably should be post to bank and get account

        $this->validateIfAccountAlreadyExist($data);
        $wallet = $this->walletRepository->create($data);

        $this->userService->updateUserWallet($data['user_id'], $wallet->id);

        return $wallet;
    }

    public function findWalletByUserId(string $id) : ?Wallet
    {
        return $this->walletRepository->findWalletByUserId($id);
    }

    private function validateIfAccountAlreadyExist(array $data): void
    {
        if ($this->walletRepository->userWalletExist($data['user_id'])) {
            throw new WalletException('Wallet already exists');
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
