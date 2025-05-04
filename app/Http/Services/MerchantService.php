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

    /**
     * Get all merchants for a customer
     * 
     * @param Customer $customer
     * @return array
     */
    public function getCustomerMerchant(Customer $customer)
    {
        return $this->merchantRepository->getCustomerMerchants($customer);
    }

    /**
     * Create a merchant
     * 
     * @param Request $request
     * @param Customer $customer
     * @return void
     */
    public function createMerchant(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'name' => 'required',
                'code' => 'required',
            ]);

            return $this->merchantRepository->createMerchant($request, $customer);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * Update a merchant
     * 
     * @param Request $request
     * @param Merchant $merchant
     * @return void
     */
    public function updateMerchant(Request $request, Merchant $merchant)
    {
        try {
            $request->validate([
                'name' => 'required',
                'product_id' => 'required',
                'code' => 'required',
            ]);

            return $this->merchantRepository->updateMerchant($request, $merchant);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * Delete a merchant
     * 
     * @param Merchant $merchant
     * @param Customer $customer
     * @return void
     */
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

    /**
     * Get all products
     * 
     * @return array
     */
    public function getProducts()
    {
        return $this->merchantRepository->getProducts();
    }

    /**
     * Get the price list for a product
     * 
     * @param string $code
     * @return array
     */
    public function getPriceList(string $code)
    {
        try {
            return $this->merchantRepository->getPriceList($code);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * Get the choice list for a product
     * 
     * @param string $code
     * @return array
     */
    public function getChoiceList(string $code)
    {
        try {
            return $this->merchantRepository->getChoiceList($code);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}
