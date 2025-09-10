<?php

namespace App\Http\Repositories;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetRepository
{
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

    public function createBudget(Request $request, Customer $customer)
    {
        return Budget::query()
            ->create([
                'amount' => $request->amount,
                'category_id' => $request->category_id,
                'customer_id' => $customer->id
            ]);
    }

    public function updateBudget(Request $request, Budget $budget)
    {
        Budget::query()->find($budget->id)->update([
            'amount' => $request->amount,
            'category_id' => $request->category_id,
        ]);

        return $budget->refresh();
    }

    public function deleteBudget(Budget $budget)
    {
        return Budget::query()->find($budget->id)->delete();
    }
}
