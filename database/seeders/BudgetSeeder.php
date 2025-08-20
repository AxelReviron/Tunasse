<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $budgets = [
            ['name' => 'Alimentation', 'amount' => 300, 'icon' => '🍎'],
            ['name' => 'Sport', 'amount' => 100, 'icon' => '🏋️‍♂️'],
            ['name' => 'Loisir', 'amount' => 150, 'icon' => '🎮'],
            ['name' => 'Factures', 'amount' => 500, 'icon' => '💡'],
        ];

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        foreach ($budgets as $budgetData) {
            Budget::factory()->create([
                'name' => $budgetData['name'],
                'amount' => $budgetData['amount'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'icon' => $budgetData['icon'],
                'user_id' => $user->id,
            ]);
        }
    }
}
