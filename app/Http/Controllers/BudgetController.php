<?php

namespace App\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Services\BudgetService;
use App\Models\Budget;
use App\Models\Customer;
use Illuminate\Http\Request;
use Throwable;

class BudgetController extends Controller
{
public function __construct(private BudgetService $budgetService) {}

    public function index(Customer $customer): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->budgetService->getCustomerBudgets($customer);
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
            $customer = $this->budgetService->createBudget($request, $customer);
            return new ApiSuccessResponse($customer, "Budget Created Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function update(Request $request, Customer $customer, Budget $budget): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->budgetService->updateBudget($request, $budget, $customer);
            return new ApiSuccessResponse($customer, "Budget Updated Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function destroy(Customer $customer, Budget $budget): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->budgetService->deleteBudget($budget, $customer);
            return new ApiSuccessResponse($customer, "Budget Deleted Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }
}

