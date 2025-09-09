<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
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

        $checkingAccount = $accounts->firstWhere('name', 'Checking');

        // Définir les transactions par budget
        $transactionsByBudget = [
            'Bills' => [
                [
                    'name' => 'Rent',
                    'date' => '01/09/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => 'month',
                    'amount' => 800,
                    'type' => 'expense',
                ],
                [
                    'name' => 'Electricity',
                    'date' => '05/09/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => 'month',
                    'amount' => 120,
                    'type' => 'expense',
                ],
                [
                    'name' => 'Internet',
                    'date' => '07/09/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => 'month',
                    'amount' => 40,
                    'type' => 'expense',
                ],
                [
                    'name' => 'Home insurance',
                    'date' => '10/09/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => 'month',
                    'amount' => 20,
                    'type' => 'expense',
                ],
                [
                    'name' => 'Car insurance',
                    'date' => '10/09/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => 'month',
                    'amount' => 80,
                    'type' => 'expense',
                ],
            ],
            'Food' => [
                [
                    'name' => 'Gasoline',
                    'date' => '04/09/2025',
                    'is_recurring' => false,
                    'amount' => 90,
                    'type' => 'expense',
                ],
                [
                    'name' => 'Restaurant',
                    'date' => '17/09/2025',
                    'is_recurring' => false,
                    'amount' => 30,
                    'type' => 'expense',
                ],
            ],
            'Entertainment' => [
                [
                    'name' => 'Movie tickets',
                    'date' => '12/09/2025',
                    'is_recurring' => false,
                    'amount' => 25,
                    'type' => 'expense',
                ],
                [
                    'name' => 'Concert',
                    'date' => '20/09/2025',
                    'is_recurring' => false,
                    'amount' => 60,
                    'type' => 'expense',
                ],
            ],
            'Sport' => [
                [
                    'name' => 'Gym membership',
                    'date' => '01/09/2025',
                    'is_recurring' => true,
                    'recurring_interval' => 1,
                    'recurring_unit' => 'month',
                    'amount' => 50,
                    'type' => 'expense',
                ],
                [
                    'name' => 'Sports equipment',
                    'date' => '15/09/2025',
                    'is_recurring' => false,
                    'amount' => 120,
                    'type' => 'expense',
                ],
            ],
            'Car' => [
                [
                    'name' => 'Gasoline',
                    'date' => '04/09/2025',
                    'is_recurring' => false,
                    'amount' => 90,
                    'type' => 'expense',
                ],
            ],
            'Other' => [
                [
                    'name' => 'Salary',
                    'date' => '01/09/2025',
                    'is_recurring' => false,
                    'amount' => 2500,
                    'type' => 'income',
                ],
            ],
        ];

        foreach ($transactionsByBudget as $budgetName => $transactions) {
            $budget = $budgetName ? $budgets->firstWhere('name', $budgetName) : null;
            $this->createTransactions($user, $checkingAccount, $budget, $transactions);
        }
    }

    /**
     * Crée les transactions pour un utilisateur, compte et budget donné.
     */
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
