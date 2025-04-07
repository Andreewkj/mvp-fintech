<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user1 = User::factory()->create([
            'full_name'  => 'Andreew Januario',
            'email' => 'andreew@gmail.com',
            'cpf'   => '31543499015',
            'cnpj'  => null,
            'phone' => '31993920022',
            'password' => Hash::make('123456'),
        ]);

        $user2 = User::factory()->create([
            'full_name'  => 'Alecssander Januario',
            'email' => 'alecssander@gmail.com',
            'cnpj'  => '55456074000129',
            'phone' => '31993920022',
            'password' => Hash::make('123456'),
        ]);

        Wallet::factory()->create([
            'user_id' => $user1,
            'balance' => 100000,
        ]);

        Wallet::factory()->create([
            'user_id' => $user2,
            'type' => 'shop_keeper',
        ]);
    }
}
