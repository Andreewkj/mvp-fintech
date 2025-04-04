<?php

namespace App\Domain\Services;

use App\Domain\Repositories\TransferRepository;
use App\Enums\WalletTypeEnum;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class TransferService
{
    protected TransferRepository $trasferRepository;

    const MINIMUM_TRANSFER_VALUE = 0;

    public function __construct()
    {
        $this->trasferRepository = new TransferRepository();
    }

    public function transfer(array $data): void
    {
        $userService = new UserService();
        $walletService = new WalletService();

        $payee = $userService->findUserById($data['payee']);
        $payer = $userService->findUserById($data['payer']);
        $value = filter_var($data['value'], FILTER_VALIDATE_INT);
        $value = $value ?? 0;

        $this->validateTransfer($payee, $payer, $value);

        $walletService->makeTransfer($payee, $payer, $value);
    }

    private function validateTransfer(User $payee, User $payer, int $amount): void
    {
        //TODO: Revisar a classe do erro

        if ($payer->wallet->type === WalletTypeEnum::SHOP_KEEPER->value) {
            throw new \InvalidArgumentException('Shop keeper cannot make transfers');
        }

        if ($amount <= self::MINIMUM_TRANSFER_VALUE) {
            throw new \InvalidArgumentException('Value must be greater than 0');
        }

        if ($payee->wallet->balance < $amount) {
            throw new \InvalidArgumentException('Insufficient balance');
        }

        if ($payee->id === $payer->id) {
            throw new \InvalidArgumentException('Payee and payer cannot be the same');
        }
    }

    public function register(array $array): void
    {
        $this->trasferRepository->register($array);
    }
}
