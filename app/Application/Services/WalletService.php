<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\Transfer;
use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Interfaces\Repositories\WalletRepositoryInterface;
use App\Domain\VO\Account;
use App\Exceptions\WalletException;
use App\Jobs\AuthorizeTransfer;
use App\Domain\Entities\Wallet;
use Illuminate\Support\Facades\DB;

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
}
