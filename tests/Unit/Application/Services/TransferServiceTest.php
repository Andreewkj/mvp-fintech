<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Services;

use App\Application\DTO\Transfer\MakeTransferDTO;
use App\Application\Factories\TransferFactory;
use App\Application\Factories\WalletFactory;
use App\Application\Services\TransferService;
use App\Application\Services\WalletService;
use App\Domain\Contracts\Adapters\BankAdapterInterface;
use App\Domain\Contracts\EventDispatcherInterface;
use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Domain\Contracts\Repositories\WalletRepositoryInterface;
use App\Domain\Contracts\TransactionManagerInterface;
use App\Domain\Entities\Transfer;
use App\Domain\Entities\Wallet;
use App\Domain\Enums\TransferStatusEnum;
use App\Domain\Exceptions\TransferException;
use App\Domain\VO\TransferValue;
use Mockery;
use PHPUnit\Framework\TestCase;

class TransferServiceTest extends TestCase
{
    private $walletRepositoryMock;
    private $transferRepositoryMock;
    private $transactionManagerMock;
    private $dispatcherMock;
    private $transferService;
    private $banckAdapterMock;
    private $userRepositoryMock;
    private $walletService;
    private $walletFactoryMock;
    private $transferFactoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletRepositoryMock = Mockery::mock(WalletRepositoryInterface::class);
        $this->transferRepositoryMock = Mockery::mock(TransferRepositoryInterface::class);
        $this->transactionManagerMock = Mockery::mock(TransactionManagerInterface::class);
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->dispatcherMock = Mockery::mock(EventDispatcherInterface::class);
        $this->banckAdapterMock = Mockery::mock(BankAdapterInterface::class);

        $this->walletFactoryMock = Mockery::mock(WalletFactory::class);
        $this->transferFactoryMock = Mockery::mock(TransferFactory::class);

        $this->walletService = new WalletService(
            $this->walletRepositoryMock,
            $this->userRepositoryMock,
            $this->walletFactoryMock
        );

        $this->transferService = new TransferService(
            $this->walletService,
            $this->transferRepositoryMock,
            $this->transactionManagerMock,
            $this->banckAdapterMock,
            $this->dispatcherMock,
            $this->transferFactoryMock
        );
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_transfer_success(): void
    {
        $payerWallet = new Wallet('wallet_payer', 'user_payer', 500, 'common');
        $payeeWallet = new Wallet('wallet_payee', 'user_payee', 100, 'common');

        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')
            ->with('payer_id')->andReturn($payerWallet);
        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')
            ->with('payee_id')->andReturn($payeeWallet);

        $transfer = new Transfer(
            'transfer_id',
            $payerWallet->getWalletId(),
            $payeeWallet->getWalletId(),
            TransferStatusEnum::STATUS_PENDING->value,
            new TransferValue(100),
            null,
            null
        );

        $this->transferFactoryMock->shouldReceive('fromDto')->andReturn($transfer);
        $this->transferRepositoryMock->shouldReceive('create')->once()->andReturn($transfer);
        $this->banckAdapterMock->shouldReceive('authorizeTransfer')->andReturn(true);
        $this->walletRepositoryMock->shouldReceive('updateBalance')->twice();
        $this->transferRepositoryMock->shouldReceive('updateToAuthorizedStatus')->once();
        $this->dispatcherMock->shouldReceive('dispatch')->once();

        $this->transactionManagerMock
            ->shouldReceive('run')
            ->once()
            ->andReturnUsing(fn($callback) => $callback());

        $dto = new MakeTransferDto('payee_id', 'payer_id', 100);
        $this->transferService->transfer($dto);

        $this->assertTrue(true); // confirmação explícita de que passou
    }

    public function test_throws_exception_when_payer_and_payee_are_same(): void
    {
        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('Payee and payer cannot be the same');

        $wallet = new Wallet('same_wallet', 'user', 1000, 'common');

        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')
            ->with('payee_id')->andReturn($wallet);
        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')
            ->with('payer_id')->andReturn($wallet);

        $dto = new MakeTransferDto('payee_id', 'payer_id', 100);
        $this->transferService->transfer($dto);
    }

    public function test_throws_exception_when_bank_denies_transfer(): void
    {
        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('Transfer was not authorized by the bank');

        $payerWallet = new Wallet('wallet_payer', 'user_payer', 500, 'common');
        $payeeWallet = new Wallet('wallet_payee', 'user_payee', 100, 'common');

        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')->with('payer_id')->andReturn($payerWallet);
        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')->with('payee_id')->andReturn($payeeWallet);

        $transfer = new Transfer(
            'transfer_id',
            $payerWallet->getWalletId(),
            $payeeWallet->getWalletId(),
            TransferStatusEnum::STATUS_PENDING->value,
            new TransferValue(100),
            null,
            null
        );

        $this->transferFactoryMock->shouldReceive('fromDto')->once()->andReturn($transfer);
        $this->transferRepositoryMock->shouldReceive('create')->once()->andReturn($transfer);
        $this->banckAdapterMock->shouldReceive('authorizeTransfer')->andReturn(false);
        $this->transferRepositoryMock->shouldReceive('updateToDeniedStatus')->once();

        $this->transactionManagerMock
            ->shouldReceive('run')
            ->once()
            ->andReturnUsing(fn($callback) => $callback());

        $dto = new MakeTransferDto('payee_id', 'payer_id', 100);
        $this->transferService->transfer($dto);
    }
}
