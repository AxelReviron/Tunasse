<?php

namespace Database\Seeders;

use App\Enums\TransactionType;
use App\Models\Account;
use Illuminate\Database\Seeder;

class RecalculateAccountBalancesSeeder extends Seeder
{
    /**
     * Recalculate all account balances based on their transactions.
     * This is needed because WithoutModelEvents disables the listener during seeding.
     */
    public function run(): void
    {
        Account::withoutGlobalScopes()->each(function (Account $account) {
            $incomeMinor = $account->transactions()
                ->where('type', TransactionType::INCOME)
                ->sum('amount');

            $expenseMinor = $account->transactions()
                ->where('type', TransactionType::EXPENSE)
                ->sum('amount');

            $balanceMinor = (int) $incomeMinor - (int) $expenseMinor;

            // Update balance directly in minor units (bypassing the mutator)
            $account->newQuery()
                ->where('id', $account->id)
                ->update(['balance' => $balanceMinor]);
        });
    }
}
