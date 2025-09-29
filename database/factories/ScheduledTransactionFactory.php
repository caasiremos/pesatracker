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
            'product_id' => 1,
            'code' =>  'OIASDF',
            'amount' => fake()->numberBetween(1000, 1000000),
            'payment_date' => fake()->dateTimeBetween('now', '+1 year'),
            'frequency' => fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
            'note' => fake()->optional()->sentence(),
            'transaction_phone_number' => fake()->phoneNumber(),
        ];
    }
}
