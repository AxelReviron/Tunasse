<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->words(),
            'amount' => $this->faker->randomFloat(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'color' => $this->faker->hexColor(),
            'currency_id' => Currency::factory(),
            'user_id' => User::factory(),
        ];
    }
}
