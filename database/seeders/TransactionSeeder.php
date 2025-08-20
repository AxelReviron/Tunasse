<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $accounts = $user->accounts;
        $budgets = $user->budgets;

        foreach ($accounts as $account) {
            Transaction::factory()
                ->count(rand(5, 15))
                ->for($user, 'user')
                ->for($accounts->random(), 'account')
                ->for($budgets->random(), 'budget')
                ->create();
        }
    }
}
