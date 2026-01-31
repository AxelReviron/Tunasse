<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            // Fiat
            ['code' => 'USD', 'name' => 'United States Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥'],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF'],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'CA$'],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$'],

            // Crypto
            ['code' => 'BTC', 'name' => 'Bitcoin', 'symbol' => '₿'],
            ['code' => 'ETH', 'name' => 'Ethereum', 'symbol' => 'Ξ'],
            ['code' => 'USDT', 'name' => 'Tether', 'symbol' => '₮'],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
