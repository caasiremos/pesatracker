<?php

namespace App\Http\Repositories;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetRepository
{
   /*
    * Get sum of budgets for a customer
    * 
    * @param Customer $customer
    * @return array
    */
    public function getCustomerBudgetSum(Customer $customer)
    {
        return Budget::query()
            ->where('customer_id', $customer->id)
            ->sum('amount');
    }

    /*
    * Get all budgets for a customer
    * 
    * @param Customer $customer
    * @return array
    */
    public function getCustomerBudgets(Customer $customer)
    {
        return Budget::query()
            ->with('category:id,name') // Only select id and name from category
            ->select([
                'id', 
                'amount', 
                'category_id',
                DB::raw('(
                    COALESCE((
                        SELECT SUM(amount) 
                        FROM cash_expenses 
                        WHERE category_id = budgets.category_id 
                        AND customer_id = ' . $customer->id . '
                    ), 0) + 
                    COALESCE((
                        SELECT SUM(amount) 
                        FROM scheduled_transactions 
                        WHERE category_id = budgets.category_id 
                        AND customer_id = ' . $customer->id . '
                    ), 0)
                ) as spent')
            ])
            ->where('customer_id', $customer->id)
            ->get();
    }

    /*
    * Create a budget
    * 
    * @param Request $request
    * @param Customer $customer
    * @return Budget
    */
    public function createBudget(Request $request, Customer $customer)
    {
        return Budget::query()
            ->create([
                'amount' => $request->amount,
                'category_id' => $request->category_id,
                'customer_id' => $customer->id
            ]);
    }

    /*
    * Update a budget
    * 
    * @param Request $request
    * @param Budget $budget
    * @return Budget
    */
    public function updateBudget(Request $request, Budget $budget)
    {
        Budget::query()->find($budget->id)->update([
            'amount' => $request->amount,
            'category_id' => $request->category_id,
        ]);

        return $budget->refresh();
    }

    /*
    * Delete a budget
    * 
    * @param Budget $budget
    * @return void
    */
    public function deleteBudget(Budget $budget)
    {
        return Budget::query()->find($budget->id)->delete();
    }
}
