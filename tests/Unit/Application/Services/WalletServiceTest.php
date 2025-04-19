<?php

namespace Tests\Unit\Application\Services;

use App\Application\Services\WalletService;
use App\Domain\Entities\Wallet;
use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Interfaces\Repositories\WalletRepositoryInterface;
use App\Exceptions\WalletException;
use PHPUnit\Framework\TestCase;

class WalletServiceTest extends TestCase
{
    private $walletRepository;
    private $userRepository;
    private $transferRepository;
    private WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletRepository = $this->createMock(WalletRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->transferRepository = $this->createMock(TransferRepositoryInterface::class);

        $this->walletService = new WalletService(
            $this->walletRepository,
            $this->userRepository,
            $this->transferRepository
        );
    }

    public function test_create_wallet_successfully()
    {
        $userId = 'user-id-123';
        $walletData = ['user_id' => $userId];

        $walletMock = $this->createMock(Wallet::class);

        $this->walletRepository
            ->expects($this->once())
            ->method('userWalletExist')
            ->with($userId)
            ->willReturn(false);

        $this->walletRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($walletMock);

        $this->userRepository
            ->expects($this->once())
            ->method('updateUserWallet')
            ->with($userId, $walletMock->getId());

        $result = $this->walletService->createWallet($walletData);

        $this->assertSame($walletMock, $result);
    }

    public function test_create_wallet_throws_exception_if_wallet_exists()
    {
        $this->expectException(WalletException::class);
        $this->expectExceptionMessage('Wallet already exists');

        $userId = 'user-id-123';
        $walletData = ['user_id' => $userId];

        $this->walletRepository
            ->expects($this->once())
            ->method('userWalletExist')
            ->with($userId)
            ->willReturn(true);

        $this->walletService->createWallet($walletData);
    }
}
