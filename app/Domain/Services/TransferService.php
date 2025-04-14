<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Enums\WalletTypeEnum;
use App\Exceptions\TransferException;
use App\Exceptions\WalletException;
use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferService
{
    const MINIMUM_TRANSFER_VALUE = 0;

    public function __construct(
        protected WalletService $walletService,
        protected TransferRepositoryInterface $transferRepository
    )
    {}

    /**
     * @throws TransferException
     * @throws WalletException
     */
    public function transfer(array $data): Transfer
    {
        $payeeWallet = $this->walletService->findWalletByUserId($data['payee_id']);
        $payerWallet = $this->walletService->findWalletByUserId(auth()->user()->id);
        $value = $data['value'];

        if ($payeeWallet === null) {
            throw new TransferException('Payee wallet not found');
        }

        if ($payerWallet === null) {
            throw new TransferException('Payer wallet not found');
        }

        $this->validateTransfer($payeeWallet, $payerWallet, $value);

        return $this->walletService->transferBetweenWallets($payeeWallet, $payerWallet, $value);
    }

    private function validateTransfer(Wallet $payeeWallet, Wallet $payerWallet, int $value): void
    {
        if ($payerWallet->type === WalletTypeEnum::SHOP_KEEPER->value) {
            throw new TransferException('Shop keeper cannot make transfers');
        }

        if ($value <= self::MINIMUM_TRANSFER_VALUE) {
            throw new TransferException('Value must be greater than 0');
        }

        if ($payerWallet->balance < $value) {
            throw new TransferException('Insufficient balance');
        }

        if ($payeeWallet->id === $payerWallet->id) {
            throw new TransferException('Payee and payer cannot be the same');
        }
    }
}
