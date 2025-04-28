<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Domain\Contracts\Repositories\WalletRepositoryInterface;
use App\Domain\Entities\Wallet;
use App\Domain\Exceptions\WalletException;
use App\Domain\VO\Account;

class WalletService
{
    public function __construct(
        protected WalletRepositoryInterface $walletRepository,
        protected UserRepositoryInterface $userRepository,
        protected TransferRepositoryInterface $transferRepository
    )
    {}

    /**
     * @throws WalletException
     */
    public function createWallet(array $data) : Wallet
    {
        $data['account'] = (new Account())->getValue();
        //Here probably should be post to bank and get account

        $this->validateIfAccountAlreadyExist($data);
        $wallet = $this->walletRepository->create($data);
        $this->userRepository->updateUserWallet($data['user_id'], $wallet->getId());

        return $wallet;
    }

    public function findWalletByUserId(string $userId) : ?Wallet
    {
        return $this->walletRepository->findWalletByUserId($userId);
    }

    /**
     * @throws WalletException
     */
    private function validateIfAccountAlreadyExist(array $data): void
    {
        if ($this->walletRepository->userWalletExist($data['user_id'])) {
            throw new WalletException('Wallet already exists');
        }
    }

    public function creditWallet(Wallet $wallet, int $value): void
    {
        $wallet->credit($value);
        $this->walletRepository->updateBalance($wallet);
    }

    public function debitWallet(Wallet $wallet, int $value): void
    {
        $wallet->debit($value);
        $this->walletRepository->updateBalance($wallet);
    }
}
