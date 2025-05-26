<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTO\Transfer\CreateTransferDTO;
use App\Application\DTO\Transfer\MakeTransferDTO;
use App\Application\Factories\TransferFactory;
use App\Domain\Contracts\Adapters\BankAdapterInterface;
use App\Domain\Contracts\EventDispatcherInterface;
use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\Contracts\TransactionManagerInterface;
use App\Domain\Entities\Transfer;
use App\Domain\Entities\Wallet;
use App\Domain\Exceptions\TransferException;
use App\Events\TransferWasCompleted;

class TransferService
{
    public function __construct(
        private WalletService               $walletService,
        private TransferRepositoryInterface $transferRepository,
        private TransactionManagerInterface $transactionManager,
        private BankAdapterInterface        $bankAdapter,
        private EventDispatcherInterface    $eventDispatcher,
        private TransferFactory             $transferFactory,
    )
    {}

    /**
     * @throws TransferException
     */
    public function transfer(MakeTransferDTO $makeTransferDTO): void
    {
        $payerWallet = $this->walletService->findWalletByUserId($makeTransferDTO->payerId);
        $payeeWallet = $this->walletService->findWalletByUserId($makeTransferDTO->payeeId);

        $payerWallet->validateTransfer($makeTransferDTO->value, $payeeWallet);

        if ($payeeWallet->getWalletId() === $payerWallet->getWalletId()) {
            throw new TransferException('Payee and payer cannot be the same');
        }

        $transferWasCompleted = $this->transactionManager->run(function () use ($payeeWallet, $payerWallet, $makeTransferDTO) {
            $createTransferDTO = new CreateTransferDTO(
                $payerWallet,
                $payeeWallet,
                $makeTransferDTO->value
            );

            $transferEntity = $this->transferFactory->fromDto($createTransferDTO);

            $transfer = $this->transferRepository->create($transferEntity);

            if (!$transfer) {
                throw new TransferException('Transfer could not be created');
            }

            if (!$this->bankAdapter->authorizeTransfer($transfer)) {
                $this->transferRepository->updateToDeniedStatus($transfer);
                return false;
            }

            $this->handleAuthorizedTransfer($transfer, $payeeWallet, $payerWallet);
            $this->eventDispatcher->dispatch(new TransferWasCompleted($transfer));

            return true;
        });

        if (!$transferWasCompleted) {
            throw new TransferException('Transfer was not authorized by the bank');

        }
    }

    private function handleAuthorizedTransfer(Transfer $transfer, Wallet $payeeWallet, Wallet $payerWallet): void
    {
        $this->walletService->creditWallet($payeeWallet, $transfer->getValue());
        $this->walletService->debitWallet($payerWallet, $transfer->getValue());

        $this->transferRepository->updateToAuthorizedStatus($transfer);
    }
}
