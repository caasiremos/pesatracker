<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashExpense>
 */
class CashExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'amount' => fake()->numberBetween(1000, 1000000),
            'payment_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'note' => fake()->optional()->sentence(),
            'attachment' => fake()->optional()->imageUrl(),
            'provider' => fake()->randomElement(['MTN', 'Airtel', 'Other']),
            'transaction_phone_number' => fake()->phoneNumber(),
            'transaction_reference' => fake()->optional()->uuid(),
            'transaction_status' => fake()->randomElement(['pending', 'completed', 'failed']),
            'telecom_product' => fake()->optional()->word(),
        ];
    }
}
