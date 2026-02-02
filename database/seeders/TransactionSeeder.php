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

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $transactionsByBudget = [
            'Bills' => [
                [
                    'label' => 'Rent',
                    'date' => "01/$currentMonth/$currentYear",
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => RecurringTransactionUnit::MONTH,
                    'amount' => 800,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Electricity',
                    'date' => "05/$currentMonth/$currentYear",
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => RecurringTransactionUnit::MONTH,
                    'amount' => 120,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Internet',
                    'date' => "07/$currentMonth/$currentYear",
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => RecurringTransactionUnit::MONTH,
                    'amount' => 40,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Home insurance',
                    'date' => "10/$currentMonth/$currentYear",
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => RecurringTransactionUnit::MONTH,
                    'amount' => 20,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Car insurance',
                    'date' => "10/$currentMonth/$currentYear",
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
                    'date' => "04/$currentMonth/$currentYear",
                    'is_recurring' => false,
                    'amount' => 90,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Restaurant',
                    'date' => "17/$currentMonth/$currentYear",
                    'is_recurring' => false,
                    'amount' => 30,
                    'type' => TransactionType::EXPENSE,
                ],
            ],
            'Entertainment' => [
                [
                    'label' => 'Movie tickets',
                    'date' => "12/$currentMonth/$currentYear",
                    'is_recurring' => false,
                    'amount' => 25,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Concert',
                    'date' => "20/$currentMonth/$currentYear",
                    'is_recurring' => false,
                    'amount' => 60,
                    'type' => TransactionType::EXPENSE,
                ],
            ],
            'Sport' => [
                [
                    'label' => 'Gym membership',
                    'date' => "01/$currentMonth/$currentYear",
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => RecurringTransactionUnit::MONTH,
                    'amount' => 50,
                    'type' => TransactionType::EXPENSE,
                ],
                [
                    'label' => 'Sports equipment',
                    'date' => "15/$currentMonth/$currentYear",
                    'is_recurring' => false,
                    'amount' => 120,
                    'type' => TransactionType::EXPENSE,
                ],
            ],
            'Car' => [
                [
                    'label' => 'Gasoline',
                    'date' => "04/$currentMonth/$currentYear",
                    'is_recurring' => false,
                    'amount' => 90,
                    'type' => TransactionType::EXPENSE,
                ],
            ],
            'Other' => [
                [
                    'label' => 'Salary',
                    'date' => "01/$currentMonth/$currentYear",
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

        // Savings account transactions
        $savingsAccount = $accounts->firstWhere('label', 'Savings');
        $savingsTransactions = [
            [
                'label' => 'Monthly savings transfer',
                'date' => "01/$currentMonth/$currentYear",
                'is_recurring' => true,
                'recurring_interval' => 1,
                'recurring_unit' => RecurringTransactionUnit::MONTH,
                'amount' => 500,
                'type' => TransactionType::INCOME,
            ],
            [
                'label' => 'Emergency fund',
                'date' => "15/$currentMonth/$currentYear",
                'is_recurring' => false,
                'amount' => 1000,
                'type' => TransactionType::INCOME,
            ],
            [
                'label' => 'Vacation savings',
                'date' => "01/$currentMonth/$currentYear",
                'is_recurring' => true,
                'recurring_interval' => 1,
                'recurring_unit' => RecurringTransactionUnit::MONTH,
                'amount' => 200,
                'type' => TransactionType::INCOME,
            ],
        ];
        $this->createTransactions($user, $savingsAccount, null, $savingsTransactions);

        // Crypto account transactions (BTC - amounts in BTC, stored as satoshis)
        $cryptoAccount = $accounts->firstWhere('label', 'Crypto');
        $cryptoTransactions = [
            [
                'label' => 'BTC purchase',
                'date' => "05/$currentMonth/$currentYear",
                'is_recurring' => false,
                'amount' => 0.025,
                'type' => TransactionType::INCOME,
            ],
            [
                'label' => 'DCA Bitcoin',
                'date' => "01/$currentMonth/$currentYear",
                'is_recurring' => true,
                'recurring_interval' => 1,
                'recurring_unit' => RecurringTransactionUnit::WEEK,
                'amount' => 0.005,
                'type' => TransactionType::INCOME,
            ],
            [
                'label' => 'Trading fees',
                'date' => "05/$currentMonth/$currentYear",
                'is_recurring' => false,
                'amount' => 0.0001,
                'type' => TransactionType::EXPENSE,
            ],
        ];
        $this->createTransactions($user, $cryptoAccount, null, $cryptoTransactions);

        // Stocks account transactions
        $stocksAccount = $accounts->firstWhere('label', 'Stocks');
        $stocksTransactions = [
            [
                'label' => 'Monthly ETF investment',
                'date' => "01/$currentMonth/$currentYear",
                'is_recurring' => true,
                'recurring_interval' => 1,
                'recurring_unit' => RecurringTransactionUnit::MONTH,
                'amount' => 300,
                'type' => TransactionType::INCOME,
            ],
            [
                'label' => 'Dividend payment',
                'date' => "15/$currentMonth/$currentYear",
                'is_recurring' => false,
                'amount' => 45.50,
                'type' => TransactionType::INCOME,
            ],
            [
                'label' => 'Broker fees',
                'date' => "01/$currentMonth/$currentYear",
                'is_recurring' => true,
                'recurring_interval' => 1,
                'recurring_unit' => RecurringTransactionUnit::MONTH,
                'amount' => 2.99,
                'type' => TransactionType::EXPENSE,
            ],
        ];
        $this->createTransactions($user, $stocksAccount, null, $stocksTransactions);
    }

    private function createTransactions($user, $account, $budget, array $transactions): void
    {
        foreach ($transactions as $transaction) {
            // Ensure account_id is set before amount (for proper decimal_places lookup in mutator)
            $data = [
                'account_id' => $account->id,
                'user_id' => $user->id,
                'budget_id' => $budget?->id,
                'date' => Carbon::createFromFormat('d/m/Y', $transaction['date'])->format('Y-m-d'),
                'label' => $transaction['label'],
                'type' => $transaction['type'],
                'is_recurring' => $transaction['is_recurring'],
                'recurring_interval' => $transaction['recurring_interval'] ?? null,
                'recurring_unit' => $transaction['recurring_unit'] ?? null,
                'amount' => $transaction['amount'],
            ];

            Transaction::create($data);
        }
    }
}
