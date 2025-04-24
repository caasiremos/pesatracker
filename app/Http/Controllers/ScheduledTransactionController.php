<?php

namespace App\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Services\ScheduledTransactionService;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class ScheduledTransactionController extends Controller
{
    public function __construct(private ScheduledTransactionService $scheduledTransactionService) {}

    public function index(Customer $customer): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->scheduledTransactionService->getCustomerScheduledTransaction($customer);
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
            $customer = $this->scheduledTransactionService->createScheduledTransaction($request, $customer);
            return new ApiSuccessResponse($customer, "Scheduled Transaction Created Successful.");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function scheduledTransactions(Request $request)
    {
        $data = $this->scheduledTransactionService->getScheduledTransactions($request);
        return Inertia::render('ScheduledTransaction', $data);
    }
}
