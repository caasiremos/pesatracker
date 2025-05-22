<?php

namespace App\Http\Controllers;

use App\Http\Services\WalletService;
use App\Exceptions\ExpectedException;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class WalletController extends Controller
{
    public function __construct(private WalletService $walletService) {}

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

    public function mtnCallback(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $walletCallback = $this->walletService->mtnCallback($request);
            return new ApiSuccessResponse($walletCallback, "Wallet Deposit successful.");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function airtelCallback(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $walletCallback = $this->walletService->airtelCallback($request);
            return new ApiSuccessResponse($walletCallback, "Wallet Deposit successful.");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function customerWallets(Request $request)
    {
        $data = $this->walletService->getCustomerWallets($request);
        return Inertia::render('Wallets', $data);
    }

    public function walletWalletTransactions(Request $request)
    {
        $data = $this->walletService->getWalletTransactions($request);
        return Inertia::render('WalletTransaction', $data);
    }

    public function relworxCollectionCallback(Request $request): ApiErrorResponse
    {
        try {
            $this->walletService->relworxCollectionCallback($request);
        } catch (ExpectedException $expectedException) {
            new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function relworxDisbursementCallback(Request $request): ApiErrorResponse
    {
        try {
            $this->walletService->relworxDisbursementCallback($request);
        } catch (ExpectedException $expectedException) {
            new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function relworxProductCallback(Request $request): ApiErrorResponse
    {
        try {
            $this->walletService->relworxProductCallback($request);
        } catch (ExpectedException $expectedException) {
            new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }
}
