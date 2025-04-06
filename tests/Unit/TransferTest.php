<?php

namespace Tests\Unit;

use App\Domain\Services\TransferService;
use App\Domain\Services\WalletService;
use App\Exceptions\TransferException;
use App\Exceptions\WalletException;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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

        $this->expectException(WalletException::class);
        $this->expectExceptionMessage('Payee wallet not found');

        $transferService->transfer([
            'payee_id' => 'payee-id',
            'payer_id' => 'payer-id',
            'value'   => 100
        ]);
    }
}
