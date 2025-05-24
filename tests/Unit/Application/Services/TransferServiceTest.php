<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Services;

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletRepositoryMock = Mockery::mock(WalletRepositoryInterface::class);
        $this->transferRepositoryMock = Mockery::mock(TransferRepositoryInterface::class);
        $this->transactionManagerMock = Mockery::mock(TransactionManagerInterface::class);
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->dispatcherMock = Mockery::mock(EventDispatcherInterface::class);
        $this->banckAdapterMock = Mockery::mock(BankAdapterInterface::class);

        $this->walletService = new WalletService(
            $this->walletRepositoryMock,
            $this->userRepositoryMock,
            $this->transferRepositoryMock
        );

        $this->transferService = new TransferService(
            $this->walletService,
            $this->transferRepositoryMock,
            $this->transactionManagerMock,
            $this->banckAdapterMock,
            $this->dispatcherMock
        );
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_transfer_success(): void
    {
        $payerWallet = Mockery::mock(Wallet::class);
        $payeeWallet = Mockery::mock(Wallet::class);

        $payerWallet->shouldReceive('getWalletId')->andReturn('wallet_payer');
        $payeeWallet->shouldReceive('getWalletId')->andReturn('wallet_payee');

        $payeeWallet->shouldReceive('credit')->with(100)->once();
        $payerWallet->shouldReceive('debit')->with(100)->once();

        $payerWallet->shouldReceive('validateTransfer')->with(100, $payeeWallet)->once();

        $this->transactionManagerMock
            ->shouldReceive('run')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $this->dispatcherMock
            ->shouldReceive('dispatch')
            ->once();

        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')
            ->with('payee_id')
            ->andReturn($payeeWallet);

        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')
            ->with('payer_id')
            ->andReturn($payerWallet);

        $mockTransfer = new Transfer(
            'transfer_id',
            'wallet_payee',
            'wallet_payer',
            TransferStatusEnum::STATUS_PENDING->value,
            new TransferValue(100),
            null,
            null
        );

        $this->transferRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn($mockTransfer);

        $this->banckAdapterMock->shouldReceive('authorizeTransfer')
            ->once()
            ->andReturn(true);

        $this->walletRepositoryMock->shouldReceive('updateBalance')
            ->with($payeeWallet)
            ->once();

        $this->walletRepositoryMock->shouldReceive('updateBalance')
            ->with($payerWallet)
            ->once();

        $this->transferRepositoryMock->shouldReceive('updateToAuthorizedStatus')
            ->with($mockTransfer)
            ->once();

        $result = $this->transferService->transfer([
            'payee_id' => 'payee_id',
            'value' => 100
        ], 'payer_id');

        $this->assertInstanceOf(Transfer::class, $result);
        $this->assertEquals('wallet_payee', $result->getPayerWalletId());
        $this->assertEquals('wallet_payer', $result->getPayeeWalletId());
    }

    public function test_throws_exception_when_payer_and_payee_are_same(): void
    {
        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('Payee and payer cannot be the same');

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldReceive('getWalletId')->andReturn('same_wallet');
        $wallet->shouldReceive('validateTransfer')->with(100, $wallet)->once();

        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')
            ->with('payee_id')->andReturn($wallet);
        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')
            ->with('payer_id')->andReturn($wallet);

        $this->transferService->transfer([
            'payee_id' => 'payee_id',
            'value' => 100
        ], 'payer_id');
    }

    public function test_throws_exception_when_transfer_could_not_be_created(): void
    {
        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('Transfer could not be created');

        $payerWallet = Mockery::mock(Wallet::class);
        $payeeWallet = Mockery::mock(Wallet::class);

        $this->transactionManagerMock
            ->shouldReceive('run')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $payerWallet->shouldReceive('getWalletId')->andReturn('wallet_payer');
        $payeeWallet->shouldReceive('getWalletId')->andReturn('wallet_payee');

        $payerWallet->shouldReceive('validateTransfer')->with(100, $payeeWallet)->once();

        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')->with('payee_id')->andReturn($payeeWallet);
        $this->walletRepositoryMock->shouldReceive('findWalletByUserId')->with('payer_id')->andReturn($payerWallet);

        $this->transferRepositoryMock->shouldReceive('create')->andReturn(null); // Simula falha

        $this->transferService->transfer([
            'payee_id' => 'payee_id',
            'value' => 100
        ], 'payer_id');
    }
}
