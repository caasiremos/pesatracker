<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PHPUnit\TextUI\Configuration\Merger;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Aremo Isaac',
            'email' => 'aremoisaac@gmail.com',
        ]);

        Customer::factory(100)->create();
        Category::factory(100)->create();
        Merchant::factory(100)->create();
        Budget::factory(100)->create();
    }
}
