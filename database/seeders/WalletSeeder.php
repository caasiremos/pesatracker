<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Member;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = Customer::create([
            'name' => 'Revenue',
            'email' => 'revenue@temsoft.com',
            'phone_number' => '0782888634',
            'password' => Hash::make('password'),
        ]);
        // Momo Transaction Fee Wallet
        $customer->wallet()->create([
            'customer_id' => $customer->id,
            'wallet_identifier' => 'PTTW-'.str_pad(Wallet::max('id') + 1, 5, '0', STR_PAD_LEFT),
            'balance' => 0,
        ]);
        // Billing Wallet
        $customer->wallet()->create([
            'customer_id' => $customer->id,
            'wallet_identifier' => 'PTBW-'.str_pad(Wallet::max('id') + 1, 5, '0', STR_PAD_LEFT),
            'balance' => 0,
        ]);
    }
}
