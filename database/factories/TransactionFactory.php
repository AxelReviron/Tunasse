<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Budget;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isRecurring = $this->faker->boolean();

        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->text(),
            'date' => $this->faker->date(),
            'is_recurring' => $this->faker->boolean(),
            'recurring_interval' => $isRecurring ? $this->faker->randomNumber() : null,
            'recurring_unit' => $isRecurring ? $this->faker->randomElement(['day', 'week', 'month', 'year']) : null,
            'location' => $this->faker->city(),
            'amount' => $this->faker->randomFloat(),
            'type' => $this->faker->randomElement(['income', 'expense']),
            'account_id' => Account::factory(),
            'user_id' => User::factory(),
            'budget_id' => Budget::factory(),
        ];
    }
}
