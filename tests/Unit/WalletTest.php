<?php

namespace Tests\Unit;

use App\Domain\Interfaces\BankAdapterInterface;
use App\Domain\Repositories\WalletRepository;
use App\Domain\Services\TransferService;
use App\Domain\Services\UserService;
use App\Domain\Services\WalletService;
use App\Enums\WalletTypeEnum;
use App\Exceptions\WalletException;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{
    private $walletRepository;
    private $userService;
    private $transferService;
    private $bankAdapter;
    private $walletService;

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
            $this->bankAdapter
        );
    }

    public function testCreateWallet()
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

//    public function testTransferBetweenWalletsSuccessfully()
//    {
//        $payeeId = Str::ulid()->toString();
//        $payeeWalletId = Str::ulid()->toString();
//        $payeeWallet = new Wallet(['id' => $payeeWalletId, 'user_id' => $payeeId, 'balance' => 10000, 'type' => WalletTypeEnum::COMMON->value]);
//        $payeeWallet->id = $payeeWalletId;
//
//        $payerId = Str::ulid()->toString();
//        $payerWalletId = Str::ulid()->toString();
//        $payerWallet = new Wallet(['id' => $payerWalletId, 'user_id' => $payerId, 'balance' => 10000, 'type' => WalletTypeEnum::COMMON->value]);
//        $payerWallet->id = $payerWalletId;
//
//        $transferId = Str::ulid()->toString();
//        $transfer = new Transfer(['id' => $transferId, 'payee_id' => $payeeId, 'payer_id' => $payerId, 'amount' => 10000]);
//        $transfer->id = $transferId;
//
//        $payeeUser = new User(['id' => $payeeId, 'name' => 'payee', 'email' => 'mFk2w@example.com', 'phone' => '1234567890']);
//        $payeeUser->id = $payeeId;
//
//        $payerUser = new User(['id' => $payerId, 'name' => 'payer', 'email' => 'mFk2w@example.com', 'phone' => '1234567890']);
//        $payerUser->id = $payerId;
//
//        $value = 100;
//
//        $this->userService->method('findUserByWalletId')
//            ->willReturnOnConsecutiveCalls($payeeUser, $payerUser);
//
//        $this->walletService->transferBetweenWallets($payeeWallet, $payerWallet, $value);
//
//        //TODO: falta testar essa porte
////        Queue::assertPushed(AuthorizeTransfer::class, function ($job) use ($transfer) {
////            return $job->transfer === 'transferId';
////        });
//
//        //TODO: testar esse metodo de validação separado
////        $this->transferService->expects($this->once())
////            ->method('validateTransfer')
////            ->with($this->equalTo($userId), $this->equalTo($walletId));
//
//        // Assert other internal methods were called if needed
//    }
}
