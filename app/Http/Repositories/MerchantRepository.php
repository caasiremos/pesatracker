<?php

namespace App\Http\Repositories;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Relworx\Product;
use App\Payment\Relworx\Products;
use Illuminate\Http\Request;

class MerchantRepository
{
    public function __construct(private Products $products){ }

    public function getCustomerMerchants(Customer $customer)
    {
        return Merchant::query()
            ->with('product')
            ->select('id', 'name', 'product_id', 'customer_id')
            ->where('customer_id', $customer->id)->get();
    }

    public function createMerchant(Request $request, Customer $customer)
    {
        return Merchant::query()
            ->create([
                'name' => $request->name,
                'customer_id' => $customer->id,
                'product_id' => $request->product_id,
                'code' => $request->code,
            ]);
    }

    public function updateMerchant(Request $request, Merchant $merchant)
    {
        Merchant::query()->find($merchant->id)->update([
            'name' => $request->name,
            'product_id' => $request->product_id,
            'code' => $request->code,
        ]);

        return $merchant->refresh();
    }

     public function deleteMerchant(Merchant $merchant)
    {
        return Merchant::query()->find($merchant->id)->delete();
    }

    public function getProducts()
    {
        return Product::query()
            ->with('priceList')
            ->where('category', '!=', 'BANK_TRANSFERS')
            ->where('has_price_list', 1)
            ->get();
    }

    public function getPriceList(string $code)
    {
        return $this->products->getPriceList($code);
    }

    public function getChoiceList(string $code)
    {
        return $this->products->getChoiceList($code);
    }
}
