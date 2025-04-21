<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Contracts\DispatcherInterface;
use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\Contracts\TransactionManagerInterface;
use App\Exceptions\TransferException;
use App\Domain\Entities\Transfer;

class TransferService
{
    public function __construct(
        protected WalletService $walletService,
        protected TransferRepositoryInterface $transferRepository,
        protected TransactionManagerInterface $transactionManager,
        protected DispatcherInterface $dispatcher
    )
    {}

    /**
     * @throws TransferException
     */
    public function transfer(array $data, String $userId): Transfer
    {
        $payeeWallet = $this->walletService->findWalletByUserId($data['payee_id']);
        $payerWallet = $this->walletService->findWalletByUserId($userId);

        $value = $data['value'];

        $payerWallet->validateTransfer($value);

        if ($payeeWallet->getId() === $payerWallet->getId()) {
            throw new TransferException('Payee and payer cannot be the same');
        }

        return $this->transactionManager->run(function () use ($payeeWallet, $payerWallet, $value) {
            $transfer = $this->transferRepository->register([
                'payee_wallet_id' => $payeeWallet->getId(),
                'payer_wallet_id' => $payerWallet->getId(),
                'value' => $value
            ]);

            if (!$transfer) {
                throw new TransferException('Transfer could not be created');
            }

            $this->dispatcher->dispatch($transfer);

            return $transfer;
        });
    }
}
