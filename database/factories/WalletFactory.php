<?php

namespace Database\Factories;

use App\Domain\VO\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'balance' => 0,
            'type'    => 'common',
            'account' => (new Account())->getValue()
        ];
    }
}
