<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Interfaces\Repositories\WalletRepositoryInterface;
use App\Domain\VO\Account;
use App\Exceptions\WalletException;
use App\Jobs\AuthorizeTransfer;
use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function __construct(
        protected WalletRepositoryInterface $walletRepository,
        protected UserRepositoryInterface $userRepository,
        protected TransferRepositoryInterface $transferRepository
    )
    {}

    public function transferBetweenWallets(Wallet $payeeWallet, Wallet $payerWallet, int $value): Transfer
    {
        try {
            $transfer = null;

            DB::transaction(function () use ($payeeWallet, $payerWallet, $value, &$transfer) {
                $transfer = $this->transferRepository->register([
                    'payee_wallet_id' => $payeeWallet->id,
                    'payer_wallet_id' => $payerWallet->id,
                    'value'   => $value
                ]);
            });

            if (is_null($transfer)) {
                throw new WalletException('Transfer could not be created');
            }

            AuthorizeTransfer::dispatch($transfer);

            return $transfer;
        } catch (WalletException $e) {
            DB::rollBack();
            throw $e;
        }
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
        $this->userRepository->updateUserWallet($data['user_id'], $wallet->id);

        return $wallet;
    }

    public function findWalletByUserId(string $userId) : ?Wallet
    {
        return $this->walletRepository->findWalletByUserId($userId);
    }

    private function validateIfAccountAlreadyExist(array $data): void
    {
        if ($this->walletRepository->userWalletExist($data['user_id'])) {
            throw new WalletException('Wallet already exists');
        }
    }

    public function chargebackPayeeValue(string $payeeId, int $value): void
    {
        $this->walletRepository->chargebackPayeeValue($payeeId, $value);
    }

    public function chargebackPayerValue(string $payerId, int $value): void
    {
        $this->walletRepository->chargebackPayerValue($payerId, $value);
    }
}
