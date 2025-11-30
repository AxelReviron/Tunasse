<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Currency;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $eur = Currency::where('code', 'EUR')->first();

        $budgets = [
            ['label' => 'Food', 'amount' => 300, 'color' => '#8dbf24'],
            ['label' => 'Sport', 'amount' => 100, 'color' => '#24bf92'],
            ['label' => 'Entertainment', 'amount' => 150, 'color' => '#bf2460'],
            ['label' => 'Bills', 'amount' => 500, 'color' => '#9f24bf'],
            ['label' => 'Car', 'amount' => 100, 'color' => '#bf5b24'],
            ['label' => 'Other', 'amount' => 0, 'color' => '#bf9b24'],
        ];

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        foreach ($budgets as $budgetData) {
            Budget::factory()->create([
                'label' => $budgetData['label'],
                'amount' => $budgetData['amount'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'color' => $budgetData['color'],
                'currency_id' => $eur->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
