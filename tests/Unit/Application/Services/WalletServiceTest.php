<?php

namespace Tests\Unit\Application\Services;

use App\Application\DTO\Wallet\CreateWalletDTO;
use App\Application\Factories\WalletFactory;
use App\Application\Services\WalletService;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Domain\Contracts\Repositories\WalletRepositoryInterface;
use App\Domain\Entities\Wallet;
use App\Domain\Exceptions\WalletException;
use PHPUnit\Framework\TestCase;

class WalletServiceTest extends TestCase
{
    private WalletRepositoryInterface $walletRepository;
    private WalletFactory $walletFactory;
    private WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletRepository = $this->createMock(WalletRepositoryInterface::class);
        $this->walletFactory = $this->createMock(WalletFactory::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->walletService = new WalletService(
            $this->walletRepository,
            $userRepository,
            $this->walletFactory
        );
    }

    public function test_create_wallet_successfully(): void
    {
        $userId = 'user-id-123';
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

        $createWalletDtoMock = new CreateWalletDTO(
            $userId,
            'common',
            0
        );

        $result = $this->walletService->createWallet($createWalletDtoMock);

        $this->assertSame($walletMock, $result);
    }

    public function test_create_wallet_throws_exception_if_wallet_exists(): void
    {
        $this->expectException(WalletException::class);
        $this->expectExceptionMessage('Wallet already exists');

        $userId = 'user-id-123';

        $this->walletRepository
            ->expects($this->once())
            ->method('userWalletExist')
            ->with($userId)
            ->willReturn(true);

        $createWalletDtoMock = new CreateWalletDTO(
            $userId,
            'common',
            0
        );

        $this->walletService->createWallet($createWalletDtoMock);
    }
}
