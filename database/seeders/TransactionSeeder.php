<?php

namespace Database\Seeders;

use App\Enums\RecurringTransactionUnit;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
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

        $checkingAccount = $accounts->firstWhere('label', 'Checking');

        $transactionsByBudget = [
            'Bills' => [
                [
                    'label' => 'Rent',
                    'date' => '01/10/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => RecurringTransactionUnit::MONTH,
                    'amount' => 800,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Electricity',
                    'date' => '05/10/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => RecurringTransactionUnit::MONTH,
                    'amount' => 120,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Internet',
                    'date' => '07/10/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => RecurringTransactionUnit::MONTH,
                    'amount' => 40,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Home insurance',
                    'date' => '10/10/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => RecurringTransactionUnit::MONTH,
                    'amount' => 20,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Car insurance',
                    'date' => '10/10/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => RecurringTransactionUnit::MONTH,
                    'amount' => 80,
                    'type' => TransactionType::EXPENSE,
                ],
            ],
            'Food' => [
                [
                    'label' => 'Gasoline',
                    'date' => '04/10/2025',
                    'is_recurring' => false,
                    'amount' => 90,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Restaurant',
                    'date' => '17/10/2025',
                    'is_recurring' => false,
                    'amount' => 30,
                    'type' => TransactionType::EXPENSE,
                ],
            ],
            'Entertainment' => [
                [
                    'label' => 'Movie tickets',
                    'date' => '12/10/2025',
                    'is_recurring' => false,
                    'amount' => 25,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Concert',
                    'date' => '20/10/2025',
                    'is_recurring' => false,
                    'amount' => 60,
                    'type' => TransactionType::EXPENSE,
                ],
            ],
            'Sport' => [
                [
                    'label' => 'Gym membership',
                    'date' => '01/10/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => RecurringTransactionUnit::MONTH,
                    'amount' => 50,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Sports equipment',
                    'date' => '15/10/2025',
                    'is_recurring' => false,
                    'amount' => 120,
                    'type' => TransactionType::EXPENSE,
                ],
            ],
            'Car' => [
                [
                    'label' => 'Gasoline',
                    'date' => '04/10/2025',
                    'is_recurring' => false,
                    'amount' => 90,
                    'type' => TransactionType::EXPENSE,
                ],
            ],
            'Other' => [
                [
                    'label' => 'Salary',
                    'date' => '01/10/2025',
                    'is_recurring' => false,
                    'amount' => 2500,
                    'type' => TransactionType::INCOME,
                ],
            ],
        ];

        foreach ($transactionsByBudget as $budgetName => $transactions) {
            $budget = $budgetName ? $budgets->firstWhere('label', $budgetName) : null;
            $this->createTransactions($user, $checkingAccount, $budget, $transactions);
        }
    }

    private function createTransactions($user, $account, $budget, array $transactions): void
    {
        foreach ($transactions as $transaction) {
            Transaction::factory()
                ->for($user, 'user')
                ->for($account, 'account')
                ->when($budget, fn ($q) => $q->for($budget, 'budget'))
                ->create(array_merge($transaction, [
                    'date' => Carbon::createFromFormat('d/m/Y', $transaction['date'])->format('Y-m-d'),
                ]));
        }
    }
}
