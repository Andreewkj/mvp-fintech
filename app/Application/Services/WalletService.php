<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTO\Wallet\CreateWalletDTO;
use App\Application\Factories\WalletFactory;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Domain\Contracts\Repositories\WalletRepositoryInterface;
use App\Domain\Entities\Wallet;
use App\Domain\Exceptions\WalletException;

class WalletService
{
    public function __construct(
        private WalletRepositoryInterface $walletRepository,
        private UserRepositoryInterface   $userRepository,
        private WalletFactory             $walletFactory
    )
    {}

    /**
     * @throws WalletException
     */
    public function createWallet(CreateWalletDTO $createWalletDto) : Wallet
    {
        $this->validateIfAccountAlreadyExist($createWalletDto);

        $walletEntity = $this->walletFactory->fromDto($createWalletDto);
        $wallet = $this->walletRepository->create($walletEntity);

        $this->userRepository->updateUserWallet($createWalletDto->userId, $wallet->getWalletId());

        return $wallet;
    }

    public function findWalletByUserId(string $userId) : ?Wallet
    {
        return $this->walletRepository->findWalletByUserId($userId);
    }

    /**
     * @throws WalletException
     */
    private function validateIfAccountAlreadyExist(CreateWalletDTO $createWalletDto): void
    {
        if ($this->walletRepository->userWalletExist($createWalletDto->userId)) {
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
