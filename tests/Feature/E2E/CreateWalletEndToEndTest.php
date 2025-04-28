<?php

namespace Tests\Feature\E2E;

use App\Domain\Enums\WalletTypeEnum;
use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateWalletEndToEndTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_wallet_e2e()
    {
        $user = UserModel::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/wallet/create', [
            'type' => WalletTypeEnum::SHOP_KEEPER->value,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Your wallet was created successfully',
            ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
        ]);

        $user->refresh();
        $this->assertNotNull($user->wallet_id);
    }
}
