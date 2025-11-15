<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->name(),
            'type' => $this->faker->randomElement(AccountType::cases()),
            'balance' => $this->faker->randomFloat(),
            'color' => $this->faker->hexColor(),
            'currency_id' => Currency::factory(),
            'user_id' => User::factory(),
        ];
    }
}
