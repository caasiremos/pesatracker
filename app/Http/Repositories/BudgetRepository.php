<?php

namespace App\Http\Repositories;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;

class BudgetRepository
{
    public function getCustomerBudgets(Customer $customer)
    {
        return Budget::query()
            ->with('category:id,name') // Only select id and name from category
            ->select(['id', 'amount', 'category_id']) // Must include category_id
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
