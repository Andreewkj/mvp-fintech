<?php

namespace Tests\Feature;

use App\Application\Services\TransferService;
use App\Application\Services\UserService;
use App\Application\Services\WalletService;
use App\Enums\WalletTypeEnum;
use App\Infra\Repositories\WalletRepository;
use App\Models\WalletModel;
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

        $wallet = new WalletModel(['id' => $walletId, 'user_id' => $userId, 'balance' => 0, 'type' => WalletTypeEnum::COMMON->value]);
        $wallet->id = $walletId;

        $this->walletRepository->method('create')->willReturn($wallet);

        $this->userService->expects($this->once())
            ->method('updateUserWallet')
            ->with($this->equalTo($userId), $this->equalTo($walletId));

        $walletService = new WalletService($this->walletRepository, $this->userService, $this->transferService);

        $result = $walletService->createWallet(['user_id' => $userId, 'amount' => 500]);

        $this->assertInstanceOf(WalletModel::class, $result);
        $this->assertEquals($walletId, $result->id);
        $this->assertEquals($userId, $result->user_id);
    }
}
