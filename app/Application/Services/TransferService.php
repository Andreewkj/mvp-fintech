<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\User;
use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Domain\VO\Cnpj;
use App\Domain\VO\Cpf;
use App\Domain\VO\Email;
use App\Domain\VO\Phone;
use App\Exceptions\TransferException;
use App\Exceptions\WalletException;
use App\Domain\Entities\Transfer;

class TransferService
{
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
        $payerUser = $this->buildPayerWithWallet();
        $value = $data['value'];

        $payerUser->validateTransfer($value);

        if ($payeeWallet->getId() === $payerUser->getWallet()->getId()) {
            throw new TransferException('Payee and payer cannot be the same');
        }

        return $this->walletService->transferBetweenWallets($payeeWallet, $payerUser->getWallet(), $value);
    }

    private function buildPayerWithWallet(): User
    {
        $payerWallet = $this->walletService->findWalletByUserId(auth()->user()->id);

        $user = new User(
            id: auth()->user()->id,
            name: auth()->user()->name,
            cpf: auth()->user()->cpf ? new Cpf(auth()->user()->cpf) : null,
            cnpj: auth()->user()->cnpj ? new Cnpj(auth()->user()->cnpj) : null,
            email: new Email(auth()->user()->email),
            phone: new Phone(auth()->user()->phone),
        );

        $user->assignWallet($payerWallet);

        return $user;
    }
}
