<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Services;

use App\Application\Services\TransferService;
use App\Application\Services\WalletService;
use App\Domain\Entities\Transfer;
use App\Domain\Entities\Wallet;
use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Domain\VO\TransferValue;
use App\Enums\TransferStatusEnum;
use App\Exceptions\TransferException;
use PHPUnit\Framework\TestCase;
use Mockery;

class TransferServiceTest extends TestCase
{
    private $walletServiceMock;
    private $transferRepositoryMock;
    private TransferService $transferService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletServiceMock = Mockery::mock(WalletService::class);
        $this->transferRepositoryMock = Mockery::mock(TransferRepositoryInterface::class);

        $this->transferService = new TransferService(
            $this->walletServiceMock,
            $this->transferRepositoryMock
        );
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testTransferSuccess(): void
    {
        $payerWallet = Mockery::mock(Wallet::class);
        $payeeWallet = Mockery::mock(Wallet::class);

        $payerWallet->shouldReceive('getId')->andReturn('wallet_payer');
        $payeeWallet->shouldReceive('getId')->andReturn('wallet_payee');

        $payerWallet->shouldReceive('validateTransfer')->with(100)->once();
        $payerWallet->shouldReceive('debit')->with(100)->once();
        $payeeWallet->shouldReceive('credit')->with(100)->once();

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
        $this->assertEquals('wallet_payer', $result->getPayerWalletId());
        $this->assertEquals('wallet_payee', $result->getPayeeWalletId());
    }

    public function testThrowsExceptionWhenPayerAndPayeeAreSame(): void
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

    public function testThrowsExceptionWhenTransferCannotBeCreated(): void
    {
        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('Transfer could not be created');

        $payerWallet = Mockery::mock(Wallet::class);
        $payeeWallet = Mockery::mock(Wallet::class);

        $payerWallet->shouldReceive('getId')->andReturn('wallet_payer');
        $payeeWallet->shouldReceive('getId')->andReturn('wallet_payee');

        $payerWallet->shouldReceive('validateTransfer')->with(100)->once();
        $payerWallet->shouldReceive('debit')->with(100)->once();
        $payeeWallet->shouldReceive('credit')->with(100)->once();

        $this->walletServiceMock->shouldReceive('findWalletByUserId')->with('payee_id')->andReturn($payeeWallet);
        $this->walletServiceMock->shouldReceive('findWalletByUserId')->with('payer_id')->andReturn($payerWallet);

        $this->transferRepositoryMock->shouldReceive('register')->andReturn(null); // Simula falha

        $this->transferService->transfer([
            'payee_id' => 'payee_id',
            'value' => 100
        ], 'payer_id');
    }
}
