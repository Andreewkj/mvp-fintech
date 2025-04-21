<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Services;

use App\Application\Services\TransferService;
use App\Application\Services\WalletService;
use App\Domain\Contracts\DispatcherInterface;
use App\Domain\Contracts\TransactionManagerInterface;
use App\Domain\Entities\Transfer;
use App\Domain\Entities\Wallet;
use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\VO\TransferValue;
use App\Enums\TransferStatusEnum;
use App\Exceptions\TransferException;
use PHPUnit\Framework\TestCase;
use Mockery;

class TransferServiceTest extends TestCase
{
    private $walletServiceMock;
    private $transferRepositoryMock;
    private $transactionManagerMock;
    private $dispatcherMock;
    private $transferService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletServiceMock = Mockery::mock(WalletService::class);
        $this->transferRepositoryMock = Mockery::mock(TransferRepositoryInterface::class);
        $this->transactionManagerMock = Mockery::mock(TransactionManagerInterface::class);
        $this->dispatcherMock = Mockery::mock(DispatcherInterface::class);

        $this->transferService = new TransferService(
            $this->walletServiceMock,
            $this->transferRepositoryMock,
            $this->transactionManagerMock,
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

        $payerWallet->shouldReceive('getId')->andReturn('wallet_payer');
        $payeeWallet->shouldReceive('getId')->andReturn('wallet_payee');

        $payerWallet->shouldReceive('validateTransfer')->with(100)->once();

        $this->transactionManagerMock
            ->shouldReceive('run')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $this->dispatcherMock
            ->shouldReceive('dispatch')
            ->once();

        $this->walletServiceMock->shouldReceive('findWalletByUserId')
            ->with('payee_id')
            ->andReturn($payeeWallet);

        $this->walletServiceMock->shouldReceive('findWalletByUserId')
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

        $this->transferRepositoryMock->shouldReceive('register')
            ->once()
            ->andReturn($mockTransfer);

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
        $wallet->shouldReceive('getId')->andReturn('same_wallet');
        $wallet->shouldReceive('validateTransfer')->with(100)->once();

        $this->walletServiceMock->shouldReceive('findWalletByUserId')
            ->with('payee_id')->andReturn($wallet);
        $this->walletServiceMock->shouldReceive('findWalletByUserId')
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

        $payerWallet->shouldReceive('getId')->andReturn('wallet_payer');
        $payeeWallet->shouldReceive('getId')->andReturn('wallet_payee');

        $payerWallet->shouldReceive('validateTransfer')->with(100)->once();

        $this->walletServiceMock->shouldReceive('findWalletByUserId')->with('payee_id')->andReturn($payeeWallet);
        $this->walletServiceMock->shouldReceive('findWalletByUserId')->with('payer_id')->andReturn($payerWallet);

        $this->transferRepositoryMock->shouldReceive('register')->andReturn(null); // Simula falha

        $this->transferService->transfer([
            'payee_id' => 'payee_id',
            'value' => 100
        ], 'payer_id');
    }
}
