<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Relworx\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Merchant>
 */
class MerchantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        return [
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'product_id' => $product->id,
            'name' => fake()->word(),
            'code' => fake()->word(),
        ];
    }
}
