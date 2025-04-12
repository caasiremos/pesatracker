<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduledTransaction>
 */
class ScheduledTransactionFactory extends Factory
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
            'merchant_id' => Merchant::inRandomOrder()->first()->id,
            'amount' => fake()->numberBetween(1000, 1000000),
            'payment_date' => fake()->dateTimeBetween('now', '+1 year'),
            'frequency' => fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
            'reference' => fake()->uuid(),
            'note' => fake()->optional()->sentence(),
            'provider' => fake()->randomElement(['mtn', 'airtel', 'other']),
            'transaction_phone_number' => fake()->phoneNumber(),
            'transaction_reference' => fake()->optional()->uuid(),
            'transaction_status' => fake()->randomElement(['pending', 'completed', 'failed']),
            'telecom_product' => fake()->optional()->word(),
        ];
    }
}
