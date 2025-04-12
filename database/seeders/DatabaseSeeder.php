<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\CashExpense;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Feedback;
use App\Models\Merchant;
use App\Models\ScheduledTransaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Database\Factories\ScheduledTransactionFactory;
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
        User::factory()->create([
            'name' => 'Aremo Isaac',
            'email' => 'aremoisaac@gmail.com',
        ]);

        Customer::factory(100)->create();
        Category::factory(100)->create();
        Merchant::factory(100)->create();
        Budget::factory(100)->create();
        ScheduledTransaction::factory(100)->create();
        CashExpense::factory(100)->create();
        Feedback::factory(100)->create();
        Wallet::factory(100)->create();
        WalletTransaction::factory(100)->create();
    }
}
