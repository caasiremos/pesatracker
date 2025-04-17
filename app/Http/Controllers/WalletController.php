<?php

namespace App\Http\Controllers;

use App\Http\Services\WalletService;
use App\Exceptions\ExpectedException;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Customer;
use Illuminate\Http\Request;
use Throwable;

class WalletController extends Controller
{
    public function __construct(private WalletService $walletService)
    {
        
    }

    public function index(Customer $customer): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->walletService->getWalletDetails($customer);
            return new ApiSuccessResponse($customer, "Success");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

     public function store(Request $request, Customer $customer): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->walletService->initiateWalletDeposit($request, $customer);
            return new ApiSuccessResponse($customer, "Deposit initiated successfully, Please enter your pin to complete the transaction.");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }
}
