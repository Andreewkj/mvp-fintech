<?php

namespace Tests\Feature;

use App\Application\Services\WalletService;
use App\Domain\Entities\Wallet;
use App\Domain\Enums\WalletTypeEnum;
use App\Domain\Exceptions\WalletException;
use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CreateWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_wallet_successfully()
    {
        $user = UserModel::factory()->create();
        $this->actingAs($user);

        $walletServiceMock = Mockery::mock(WalletService::class);
        $walletServiceMock->shouldReceive('createWallet')
            ->once()
            ->andReturn(Mockery::mock(Wallet::class));

        $this->app->instance(WalletService::class, $walletServiceMock);

        $payload = [
            'type' => WalletTypeEnum::SHOP_KEEPER->value,
        ];

        $response = $this->postJson('/api/wallet/create', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Your wallet was created successfully',
            ]);
    }

    public function test_it_handles_wallet_exception()
    {
        $user = UserModel::factory()->create();
        $this->actingAs($user);

        $walletServiceMock = Mockery::mock(WalletService::class);
        $walletServiceMock->shouldReceive('createWallet')
            ->andThrow(new WalletException('Wallet already exists'));

        $this->app->instance(WalletService::class, $walletServiceMock);

        $response = $this->postJson('/api/wallet/create', [
            'type' => WalletTypeEnum::SHOP_KEEPER->value,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Wallet already exists',
            ]);
    }
}
