<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Repositories\MerchantRepository;
use App\Models\Customer;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Throwable;

class MerchantService
{
    public function __construct(private MerchantRepository $merchantRepository) {}

    public function getCustomerMerchant(Customer $customer)
    {
        return $this->merchantRepository->getCustomerMerchants($customer);
    }

    public function createMerchant(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);

            return $this->merchantRepository->createMerchant($request, $customer);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    public function updateMerchant(Request $request, Merchant $merchant)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);

            return $this->merchantRepository->updateMerchant($request, $merchant);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    public function deleteMerchant(Merchant $merchant, Customer $customer)
    {
        try {
            $merchant = Merchant::query()
                ->where('customer_id', $customer->id)
                ->where('id', $merchant->id)
                ->first();

            if(!$merchant){
                throw new ExpectedException("Merchant not found or does not belong to customer");
            }

            return $this->merchantRepository->deleteMerchant($merchant);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}
