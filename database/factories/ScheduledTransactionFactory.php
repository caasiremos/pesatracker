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


[2025-09-29 10:56:24] local.INFO: {
    "account_no": "REL08CACA5DDF",
    "reference": "c031961c-f7c3-4ce1-bd09-1c7eb8c93f8a",
    "msisdn": "14333680131",
    "amount": 1000,
    "product_code": "14333680131",
    "contact_phone": "256786966244",
    "location_id": null
}

[2025-09-29 10:56:25] local.INFO: {
    "success": false,
    "message": "Invalid product code.",
    "error_code": "INVALID_PARAMETER_VALUE"
}
