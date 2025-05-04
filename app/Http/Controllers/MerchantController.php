<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Throwable;
use App\Exceptions\ExpectedException;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Services\MerchantService;
use App\Models\Merchant;

class MerchantController extends Controller
{
    public function __construct(private MerchantService $merchantService) {}

    /**
     * Get all merchants for a customer
     * 
     * @param Customer $customer
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(Customer $customer): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->merchantService->getCustomerMerchant($customer);
            return new ApiSuccessResponse($customer, "Success");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    /**
     * Create a merchant
     * 
     * @param Request $request
     * @param Customer $customer
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(Request $request, Customer $customer): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->merchantService->createMerchant($request, $customer);
            return new ApiSuccessResponse($customer, "Merchant Created Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    /**
     * Update a merchant
     * 
     * @param Request $request
     * @param Customer $customer
     * @param Merchant $merchant
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request, Customer $customer, Merchant $merchant): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->merchantService->updateMerchant($request, $merchant);
            return new ApiSuccessResponse($customer, "Merchant Updated Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    /**
     * Delete a merchant
     * 
     * @param Customer $customer
     * @param Merchant $merchant
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy(Customer $customer, Merchant $merchant): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->merchantService->deleteMerchant($merchant, $customer);
            return new ApiSuccessResponse($customer, "Merchant Deleted Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    /**
     * Get all products
     * 
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function getProducts(): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $products = $this->merchantService->getProducts();
            return new ApiSuccessResponse($products, "Products Fetched Successful");
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    /**
     * Get the price list for a product
     * 
     * @param Request $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function getPriceList(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $priceList = $this->merchantService->getPriceList($request->code);
            return new ApiSuccessResponse($priceList, "Price List Fetched Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    /**
     * Get the choice list for a product
     * 
     * @param Request $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function getChoiceList(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $choiceList = $this->merchantService->getChoiceList($request->code);
            return new ApiSuccessResponse($choiceList, "Choice List Fetched Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }
}
