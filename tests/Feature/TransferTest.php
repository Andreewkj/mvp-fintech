<?php

namespace Tests\Unit;

use App\Domain\Interfaces\Adapters\BankAdapterInterface;
use App\Domain\Repositories\WalletRepository;
use App\Domain\Services\TransferService;
use App\Domain\Services\UserService;
use App\Domain\Services\WalletService;
use App\Enums\WalletTypeEnum;
use App\Exceptions\TransferException;
use App\Exceptions\WalletException;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletRepository = $this->createMock(WalletRepository::class);
        $this->userService = $this->createMock(UserService::class);
        $this->transferService = $this->createMock(TransferService::class);
        $this->bankAdapter = $this->createMock(BankAdapterInterface::class);

        $this->walletService = new WalletService(
            $this->walletRepository,
            $this->userService,
            $this->transferService,
        );
    }

    public function testTransferFailsWhenPayeeWalletNotFound()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $mockWalletService = $this->createMock(WalletService::class);
        $mockWalletService->method('findWalletByUserId')
            ->willReturnCallback(function ($userId) {
                if ($userId === 'payee-id') {
                    return null;
                } elseif ($userId === 'payer-id') {
                    return new Wallet(['id' => $userId]);
                }
                return null;
            });

        $transferService = new TransferService($mockWalletService);

        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('Payee wallet not found');

        $transferService->transfer([
            'payee_id' => 'payee-id',
            'payer_id' => 'payer-id',
            'value'   => 100
        ]);
    }

    public function testTransferBetweenWalletsFailure()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) { return $callback(); });

        DB::shouldReceive('rollBack')->once();

        $this->expectException(WalletException::class);

        $payeeWallet = new Wallet();
        $payeeWallet->id = Str::ulid()->toString();
        $payerWallet = new Wallet();
        $payerWallet->id = Str::ulid()->toString();

        $payeeUser = new User();
        $payeeUser->id = Str::ulid()->toString();
        $payerUser = new User();
        $payerUser->id = Str::ulid()->toString();

        $this->userService->method('findUserByWalletId')
            ->willReturnCallback(function ($id) use ($payeeUser, $payerUser, $payeeWallet, $payerWallet) {
                if ($id === $payeeWallet->id) {
                    return $payeeUser;
                } elseif ($id === $payerWallet->id) {
                    return $payerUser;
                }
                return null;
            });

        $this->transferService->method('register')
            ->willReturn(null);

        $value = 100;
        $this->walletService->transferBetweenWallets($payeeWallet, $payerWallet, $value);
    }

    public function testTransferBetweenWalletsSuccessfully()
    {
        Bus::fake();

        $payeeId = Str::ulid()->toString();
        $payeeWalletId = Str::ulid()->toString();
        $payeeWallet = new Wallet(['id' => $payeeWalletId, 'user_id' => $payeeId, 'balance' => 10000, 'type' => WalletTypeEnum::COMMON->value]);
        $payeeWallet->id = $payeeWalletId;

        $payerId = Str::ulid()->toString();
        $payerWalletId = Str::ulid()->toString();
        $payerWallet = new Wallet(['id' => $payerWalletId, 'user_id' => $payerId, 'balance' => 10000, 'type' => WalletTypeEnum::COMMON->value]);
        $payerWallet->id = $payerWalletId;

        $transferId = Str::ulid()->toString();
        $transfer = new Transfer(['id' => $transferId, 'payee_id' => $payeeId, 'payer_id' => $payerId, 'amount' => 10000]);
        $transfer->id = $transferId;

        $payeeUser = new User(['id' => $payeeId, 'name' => 'payee', 'email' => 'mFk2w@example.com', 'phone' => '1234567890']);
        $payeeUser->id = $payeeId;

        $payerUser = new User(['id' => $payerId, 'name' => 'payer', 'email' => 'mFk2w@example.com', 'phone' => '1234567890']);
        $payerUser->id = $payerId;

        $value = 100;

        $transferId = Str::ulid()->toString();
        $transfer = new Transfer(['id' => $transferId, 'payee_id' => $payeeId, 'payer_id' => $payerId, 'amount' => 10000]);
        $transfer->id = $transferId;

        $this->transferService->method('register')
            ->willReturn($transfer);

        $this->userService->method('findUserByWalletId')
            ->willReturnCallback(function ($id) use ($payeeUser, $payerUser, $payeeWallet, $payerWallet) {
                if ($id === $payeeWallet->id) {
                    return $payeeUser;
                } elseif ($id === $payerWallet->id) {
                    return $payerUser;
                }
                return null;
            });

        $result = $this->walletService->transferBetweenWallets($payeeWallet, $payerWallet, $value);

        $this->assertInstanceOf(Transfer::class, $result);
    }
}
