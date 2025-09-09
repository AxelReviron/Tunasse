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
            'name' => 'Checking',
            'type' => 'checking',
            'balance' => 2500,
            'icon' => 'CreditCard',
            'currency_id' => $eur->id,
            'user_id' => $user->id,
        ]);

        Account::factory()->create([
            'name' => 'Savings',
            'type' => 'savings',
            'balance' => 42000,
            'icon' => 'PiggyBank',
            'currency_id' => $eur->id,
            'user_id' => $user->id,
        ]);

        Account::factory()->create([
            'name' => 'Crypto',
            'type' => 'investment',
            'balance' => 9000,
            'icon' => 'Bitcoin',
            'currency_id' => $btc->id,
            'user_id' => $user->id,
        ]);

        Account::factory()->create([
            'name' => 'Stocks',
            'type' => 'investment',
            'balance' => 16000,
            'icon' => 'ChartSpline',
            'currency_id' => $eur->id,
            'user_id' => $user->id,
        ]);
    }
}
