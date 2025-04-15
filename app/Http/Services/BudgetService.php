<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Repositories\BudgetRepository;
use App\Models\Budget;
use App\Models\Customer;
use Illuminate\Http\Request;
use Throwable;

class BudgetService
{
    public function __construct(private BudgetRepository $budgetRepository) {}

    public function getCustomerBudgets(Customer $customer)
    {
        return $this->budgetRepository->getCustomerBudgets($customer);
    }

    public function createBudget(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'amount' => 'required|integer',
                'category_id' => [
                    'required',
                    'exists:categories,id',
                    // Add a custom rule to verify the category belongs to the customer
                    function ($attribute, $value, $fail) use ($customer) {
                        $categoryExists = $customer->categories()->where('id', $value)->exists();
                        if (!$categoryExists) {
                            $fail('The selected category does not belong to this customer.');
                        }
                    },
                ]
            ]);

            return $this->budgetRepository->createBudget($request, $customer);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    public function updateBudget(Request $request, Budget $budget, Customer $customer)
    {
        try {
             $request->validate([
                'amount' => 'required',
                'category_id' => [
                    'required',
                    'exists:categories,id',
                    // Add a custom rule to verify the category belongs to the customer
                    function ($attribute, $value, $fail) use ($customer) {
                        $categoryExists = $customer->categories()->where('id', $value)->exists();
                        if (!$categoryExists) {
                            $fail('The selected category does not belong to this customer.');
                        }
                    },
                ]
            ]);

            return $this->budgetRepository->updateBudget($request, $budget);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    public function deleteBudget(Budget $budget, Customer $customer)
    {
        try {
            $budget = Budget::query()
                ->where('customer_id', $customer->id)
                ->where('id', $budget->id)
                ->first();

            if (!$budget) {
                throw new ExpectedException("Budget not found or does not belong to customer");
            }

            return $this->budgetRepository->deleteBudget($budget);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}
