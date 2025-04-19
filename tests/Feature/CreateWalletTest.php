<?php

namespace Tests\Feature;

use App\Domain\Entities\Wallet;
use App\Enums\WalletTypeEnum;
use App\Exceptions\WalletException;
use App\Http\Requests\CreateWalletRequest;
use App\Application\Services\WalletService;
use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class CreateWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_wallet_successfully()
    {
        // Cria um usuário logado
        $user = UserModel::factory()->create();
        $this->actingAs($user);

        // Mock do service
        $walletServiceMock = Mockery::mock(WalletService::class);
        $walletServiceMock->shouldReceive('createWallet')
            ->once()
            ->andReturn(Mockery::mock(Wallet::class));

        $this->app->instance(WalletService::class, $walletServiceMock);

        // Dados do request
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

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Wallet already exists',
            ]);
    }
}
