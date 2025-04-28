<?php

namespace Tests\Feature;

use App\Events\TransferWasCompleted;
use App\Models\UserModel;
use App\Models\WalletModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MakeTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_transfer(): void
    {
        Event::fake();

        $payer = UserModel::factory()->create();
        $payee = UserModel::factory()->create();

        $payerWallet = WalletModel::factory()->create([
            'user_id' => $payer->id,
            'balance' => 1000,
        ]);

        $payeeWallet = WalletModel::factory()->create([
            'user_id' => $payee->id,
            'balance' => 0,
        ]);

        $payload = [
            'payee_id' => $payee->id,
            'value' => 100,
        ];

        $response = $this->actingAs($payer)->postJson('/api/transfer/create', $payload);

        $response->assertCreated()
            ->assertJson([
                'message' => 'transfer completed successfully'
            ]);

        $this->assertDatabaseHas('transfers', [
            'payer_wallet_id' => $payerWallet->id,
            'payee_wallet_id' => $payeeWallet->id,
            'value' => 100,
        ]);

        Event::assertDispatched(TransferWasCompleted::class);
    }

    public function test_cannot_transfer_to_self(): void
    {
        $user = UserModel::factory()->create();
        WalletModel::factory()->create(['user_id' => $user->id, 'balance' => 1000]);

        $payload = [
            'payee_id' => $user->id,
            'value' => 100,
        ];

        $response = $this->actingAs($user)->postJson('/api/transfer/create', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Payee and payer cannot be the same'
            ]);
    }

    public function test_invalid_transfer_data(): void
    {
        $user = UserModel::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/transfer/create', [
            'payee_id' => 'invalid_id',
        ]);

        $response->assertStatus(422);
    }

    public function test_unauthorized_user(): void
    {
        $response = $this->postJson('/api/transfer/create', [
            'payee_id' => 1,
            'value' => 100
        ]);

        $response->assertUnauthorized();
    }
}
