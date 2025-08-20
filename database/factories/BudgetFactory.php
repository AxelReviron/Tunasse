<?php

namespace Database\Factories;

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
            'name' => $this->faker->words(),
            'amount' => $this->faker->randomFloat(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'icon' => $this->faker->name,
            'user_id' => User::factory(),
        ];
    }
}
