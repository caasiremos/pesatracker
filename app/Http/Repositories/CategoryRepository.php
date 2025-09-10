<?php

namespace App\Http\Repositories;

use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryRepository
{
    /**
     * Get all categories for a customer
     * 
     * @param Customer $customer
     * @return array
     */
    public function getCustomerCategories(Customer $customer)
    {
        return Category::query()
            ->select('id', 'name')
            ->where('customer_id', $customer->id)->get();
    }

    /**
     * Create a category
     * 
     * @param Request $request
     * @param Customer $customer
     * @return Category
     */
    public function createCategory(Request $request, Customer $customer)
    {
        return Category::query()
            ->create([
                'name' => $request->name,
                'customer_id' => $customer->id
            ]);
    }

    /**
     * Update a category
     * 
     * @param Request $request
     * @param Category $category
     * @return Category
     */
    public function updateCategory(Request $request, Category $category)
    {
        Category::query()->find($category->id)->update([
            'name' => $request->name,
        ]);

        return $category->refresh();
    }

    /**
     * Delete a category
     * 
     * @param Category $category
     * @return void
     */
    public function deleteCategory(Category $category)
    {
        return Category::query()->find($category->id)->delete();
    }

    /**
     * Get categories with spent amount
     * 
     * @param Customer $customer
     * @return array
     */
    public function getCategoriesWithSpentAmount(Customer $customer)
    {
        return Category::query()
            ->select([
                'id',
                'name',
                DB::raw('(
                    COALESCE((
                        SELECT SUM(amount) 
                        FROM cash_expenses 
                        WHERE category_id = categories.id 
                        AND customer_id = ' . $customer->id . '
                    ), 0) + 
                    COALESCE((
                        SELECT SUM(amount) 
                        FROM scheduled_transactions 
                        WHERE category_id = categories.id 
                        AND customer_id = ' . $customer->id . '
                    ), 0)
                ) as spent')
            ])
            ->where('customer_id', $customer->id)
            ->orderBy('spent', 'desc')
            ->get();
    }
}
