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

    public function getCustomerWallets(Request $request)
    {
        $search = $request->input('search');

        $wallets = Wallet::query()
            ->with('customer')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('wallet_identifier', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(7)
            ->withQueryString();

        return [
            'wallets' => $wallets,
            'filters' => ['search' => $search]
        ];
    }
}
