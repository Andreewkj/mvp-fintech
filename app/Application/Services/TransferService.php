<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Contracts\Adapters\BankAdapterInterface;
use App\Domain\Contracts\DispatcherInterface;
use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\Contracts\Repositories\WalletRepositoryInterface;
use App\Domain\Contracts\TransactionManagerInterface;
use App\Domain\Entities\Wallet;
use App\Exceptions\TransferException;
use App\Domain\Entities\Transfer;
use App\Jobs\NotifyPayee;

class TransferService
{
    public function __construct(
        protected WalletRepositoryInterface $walletRepository,
        protected TransferRepositoryInterface $transferRepository,
        protected TransactionManagerInterface $transactionManager,
        protected BankAdapterInterface $bankAdapter,
        protected DispatcherInterface $dispatcher
    )
    {}

    /**
     * @throws TransferException
     */
    public function transfer(array $data, String $userId): Transfer
    {
        $payeeWallet = $this->walletRepository->findWalletByUserId($data['payee_id']);
        $payerWallet = $this->walletRepository->findWalletByUserId($userId);

        $value = $data['value'];

        $payerWallet->validateTransfer($value);

        if ($payeeWallet->getId() === $payerWallet->getId()) {
            throw new TransferException('Payee and payer cannot be the same');
        }

        return $this->transactionManager->run(function () use ($payeeWallet, $payerWallet, $value) {
            // Create transfer with pending status to have the history of transfers even if the transfer fails
            $transfer = $this->transferRepository->register([
                'payee_wallet_id' => $payeeWallet->getId(),
                'payer_wallet_id' => $payerWallet->getId(),
                'value' => $value
            ]);

            if (!$transfer) {
                throw new TransferException('Transfer could not be created');
            }

            if (!$this->bankAdapter->authorizeTransfer($transfer)) {
                $this->transferRepository->updateToDeniedStatus($transfer);
                throw new TransferException('Transfer was not authorized');
            }

            $this->handleAuthorizedTransfer($transfer, $payeeWallet, $payerWallet);

            $this->dispatcher->dispatch(new NotifyPayee($transfer));

            return $transfer;
        });
    }

    private function handleAuthorizedTransfer(Transfer $transfer, Wallet $payeeWallet, Wallet $payerWallet): void
    {
        $payeeWallet->credit($transfer->getValue());
        $payerWallet->debit($transfer->getValue());

        $this->walletRepository->updateBalance($payeeWallet);
        $this->walletRepository->updateBalance($payerWallet);
        $this->transferRepository->updateToAuthorizedStatus($transfer);
    }
}
