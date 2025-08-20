<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $eur = Currency::where('code', 'EUR')->first();
        $btc = Currency::where('code', 'BTC')->first();

        Account::factory()->create([
            'name' => 'Compte courant',
            'type' => 'checking',
            'balance' => 1000,
            'currency_id' => $eur->id,
            'user_id' => $user->id,
        ]);

        Account::factory()->create([
            'name' => 'Compte épargne',
            'type' => 'savings',
            'balance' => 5000,
            'currency_id' => $eur->id,
            'user_id' => $user->id,
        ]);

        Account::factory()->create([
            'name' => 'Compte investissement crypto',
            'type' => 'investment',
            'balance' => 2000,
            'currency_id' => $btc->id,
            'user_id' => $user->id,
        ]);

        Account::factory()->create([
            'name' => 'Compte investissement actions',
            'type' => 'investment',
            'balance' => 3000,
            'currency_id' => $eur->id,
            'user_id' => $user->id,
        ]);
    }
}
