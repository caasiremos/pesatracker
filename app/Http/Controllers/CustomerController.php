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

    public function store(Request $request): ApiSuccessResponse|ApiErrorResponse
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

    public function login(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->customerService->login($request);
            return new ApiSuccessResponse($customer, "Customer Login Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function logout(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->customerService->logout($request);
            return new ApiSuccessResponse($customer, "Customer Logout Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    /**
     * Send OTP to customer
     * @param  Request  $request - params
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function sendOtp(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $response = $this->customerService->sendOtp();

            return new ApiSuccessResponse($response);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    /**
     * Verify OTP submitted
     *
     * @param  Request  $request - params
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function verifyOtp(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $response = $this->customerService->verifyOtp($request);

            return new ApiSuccessResponse($response);
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }
}
