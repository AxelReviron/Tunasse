<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Currency;
use App\Models\User;
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
            'label' => 'Checking',
            'type' => AccountType::CHECKING,
            'balance' => 2500,
            'icon' => 'CreditCard',
            'currency_id' => $eur->id,
            'user_id' => $user->id,
        ]);

        Account::factory()->create([
            'label' => 'Savings',
            'type' => AccountType::SAVINGS,
            'balance' => 42000,
            'icon' => 'PiggyBank',
            'currency_id' => $eur->id,
            'user_id' => $user->id,
        ]);

        Account::factory()->create([
            'label' => 'Crypto',
            'type' => AccountType::INVESTMENT,
            'balance' => 9000,
            'icon' => 'Bitcoin',
            'currency_id' => $btc->id,
            'user_id' => $user->id,
        ]);

        Account::factory()->create([
            'label' => 'Stocks',
            'type' => AccountType::INVESTMENT,
            'balance' => 16000,
            'icon' => 'ChartSpline',
            'currency_id' => $eur->id,
            'user_id' => $user->id,
        ]);
    }
}
