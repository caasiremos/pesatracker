<?php

namespace App\Http\Repositories;

use App\Models\Customer;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Utils\PhoneNumberUtil;
use Illuminate\Http\Request;

class WalletRepository
{
    public function getWalletDetails(Customer $customer)
    {
        return Wallet::query()
            ->select('customer_id', 'wallet_identifier', 'balance')
            ->where('customer_id', $customer->id)
            ->first();
    }

    public function initiateWalletDeposit(Request $request, Customer $customer)
    {
        $walletTransactions = WalletTransaction::query()
            ->create([
                'customer_id' => $customer->id,
                'wallet_id' => $customer->wallet->id,
                'amount' => $request->amount,
                'provider' => PhoneNumberUtil::provider($request->phone_number),
                'transaction_phone_number' => $request->phone_number,
            ]);

            return $walletTransactions;
    }
}
