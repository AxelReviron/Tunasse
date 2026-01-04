<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // TODO: Ajouter demo_mode pour les seeders
        $this->call([
            UserSeeder::class,
            CurrencySeeder::class,
            AccountSeeder::class,
            BudgetSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
