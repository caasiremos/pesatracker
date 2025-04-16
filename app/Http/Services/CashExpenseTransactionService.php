<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Repositories\CashExpenseTransactionRepository;
use App\Models\Customer;
use Illuminate\Http\Request;
use Throwable;

class CashExpenseTransactionService
{
    public function __construct(private CashExpenseTransactionRepository $cashExpenseTransaction)
    {
        
    }

    public function getCashExpenseTransaction(Customer $customer)
    {
        return $this->cashExpenseTransaction->getCashExpenseTransactions($customer);
    }

    public function createCashExpenseTransaction(Request $request, Customer $customer)
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
                ],
            ]);

            return $this->cashExpenseTransaction->createCashExpenseTransaction($request, $customer);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}