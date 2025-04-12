<?php

namespace App\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Services\CustomerService;
use Illuminate\Http\Request;
use Throwable;

class CustomerController extends Controller
{
    public function __construct(private CustomerService $customerService) {}

    public function store(Request $request)
    {
        try {
            $customer = $this->customerService->register($request);
            return new ApiSuccessResponse($customer, "Customer Registration Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }
}
