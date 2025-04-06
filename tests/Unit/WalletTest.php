<?php

namespace Tests\Unit;

use App\Domain\Repositories\WalletRepository;
use App\Domain\Services\TransferService;
use App\Domain\Services\UserService;
use App\Domain\Services\WalletService;
use App\Enums\WalletTypeEnum;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletRepository = $this->createMock(WalletRepository::class);
        $this->userService = $this->createMock(UserService::class);
        $this->transferService = $this->createMock(TransferService::class);

        $this->walletService = new WalletService(
            $this->walletRepository,
            $this->userService,
            $this->transferService,
        );
    }

    public function testCreateWalletSuccessfully()
    {
        $userId = Str::ulid()->toString();
        $walletId = Str::ulid()->toString();

        $wallet = new Wallet(['id' => $walletId, 'user_id' => $userId, 'balance' => 0, 'type' => WalletTypeEnum::COMMON->value]);
        $wallet->id = $walletId;

        $this->walletRepository->method('create')->willReturn($wallet);

        $this->userService->expects($this->once())
            ->method('updateUserWallet')
            ->with($this->equalTo($userId), $this->equalTo($walletId));

        $walletService = new WalletService($this->walletRepository, $this->userService, $this->transferService);

        $result = $walletService->createWallet(['user_id' => $userId, 'amount' => 500]);

        $this->assertInstanceOf(Wallet::class, $result);
        $this->assertEquals($walletId, $result->id);
        $this->assertEquals($userId, $result->user_id);
    }
}
