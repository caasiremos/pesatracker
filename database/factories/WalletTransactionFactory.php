<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;
use App\Models\Wallet;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WalletTransaction>
 */
class WalletTransactionFactory extends Factory
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
            'wallet_id' => Wallet::inRandomOrder()->first()->id,
            'amount' => fake()->numberBetween(1000, 100000),
            'type' => fake()->randomElement(['deposit', 'withdrawal']),
            'provider' => fake()->randomElement(['MTN', 'Airtel', 'Bank']),
            'transaction_phone_number' => fake()->phoneNumber(),
            'transaction_reference' => fake()->uuid(),
            'transaction_status' => fake()->randomElement(['pending', 'completed', 'failed']),
            'telecom_product' => fake()->randomElement(['Mobile Money', 'Airtime', null]),
        ];
    }
}
