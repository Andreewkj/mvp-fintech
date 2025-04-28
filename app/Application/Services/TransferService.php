<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Contracts\Adapters\BankAdapterInterface;
use App\Domain\Contracts\EventDispatcherInterface;
use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\Contracts\TransactionManagerInterface;
use App\Domain\Entities\Transfer;
use App\Domain\Entities\Wallet;
use App\Domain\Exceptions\TransferException;
use App\Events\TransferWasCompleted;
use App\Jobs\NotifyPayee;
use Exception;

class TransferService
{
    public function __construct(
        protected WalletService $walletService,
        protected TransferRepositoryInterface $transferRepository,
        protected TransactionManagerInterface $transactionManager,
        protected BankAdapterInterface $bankAdapter,
        protected EventDispatcherInterface $eventDispatcher
    )
    {}

    /**
     * @throws TransferException | Exception
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

            $this->eventDispatcher->dispatch(new TransferWasCompleted($transfer));

            return $transfer;
        });
    }

    private function handleAuthorizedTransfer(Transfer $transfer, Wallet $payeeWallet, Wallet $payerWallet): void
    {
        $this->walletService->creditWallet($payeeWallet, $transfer->getValue());
        $this->walletService->debitWallet($payerWallet, $transfer->getValue());

        $this->transferRepository->updateToAuthorizedStatus($transfer);
    }
}
