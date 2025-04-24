<?php

namespace App\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Services\CashExpenseTransactionService;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class CashExpenseTransactionController extends Controller
{
    public function __construct(private CashExpenseTransactionService $cashExpenseTransaction) {}

    public function index(Customer $customer): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->cashExpenseTransaction->getCustomerCashExpenseTransaction($customer);
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
            $customer = $this->cashExpenseTransaction->createCashExpenseTransaction($request, $customer);
            return new ApiSuccessResponse($customer, "Cash Expense Created Successful, Please complete the transaction by entering your mobile money pin!");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function cashExpenseTransactions(Request $request)
    {
        $data = $this->cashExpenseTransaction->getCashExpenseTransaction($request);
        return Inertia::render('CashTransaction', $data);
    }
}
