<?php

namespace Database\Seeders;

use App\Models\UserModel;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\WalletModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user1 = UserModel::factory()->create([
            'full_name'  => 'Andreew Januario',
            'email' => 'andreew@gmail.com',
            'cpf'   => '31543499015',
            'cnpj'  => null,
            'phone' => '31993920022',
            'password' => Hash::make('123456'),
        ]);

        $user2 = UserModel::factory()->create([
            'full_name'  => 'Alecssander Januario',
            'email' => 'alecssander@gmail.com',
            'cnpj'  => '55456074000129',
            'phone' => '31993920022',
            'password' => Hash::make('123456'),
        ]);

        $wallet1 = WalletModel::factory()->create([
            'user_id' => $user1,
            'balance' => 100000,
        ]);

        $wallet2 = WalletModel::factory()->create([
            'user_id' => $user2,
            'type' => 'shop_keeper',
        ]);

        $user1->update(['wallet_id' => $wallet1->id]);
        $user2->update(['wallet_id' => $wallet2->id]);
    }
}
