<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Customer;
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
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'amount' => fake()->numberBetween(1000, 100000),
        ];
    }
}
