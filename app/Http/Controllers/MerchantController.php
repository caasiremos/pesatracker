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

    public function index(Customer $customer)
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

    public function store(Request $request, Customer $customer)
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

    public function update(Request $request, Customer $customer, Merchant $merchant)
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

     public function destroy(Customer $customer, Merchant $merchant)
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
}
