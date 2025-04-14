<?php

namespace App\Http\Respositories;

use App\Models\Customer;
use App\Models\Merchant;
use Illuminate\Http\Request;

class MerchantRepository
{
    public function getCustomerMerchants(Customer $customer)
    {
        return Merchant::query()
            ->select('name')
            ->where('customer_id', $customer->id)->get();
    }

    public function createMerchant(Request $request, Customer $customer)
    {
        return Merchant::query()
            ->create([
                'name' => $request->name,
                'customer_id' => $customer->id
            ]);
    }

    public function updateMerchant(Request $request, Merchant $merchant)
    {
        Merchant::query()->find($merchant->id)->update([
            'name' => $request->name,
        ]);

        return $merchant->refresh();
    }

     public function deleteMerchant(Merchant $merchant)
    {
        return Merchant::query()->find($merchant->id)->delete();
    }
}
