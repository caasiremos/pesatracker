<?php

namespace App\Http\Respositories;

use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;

class CategoryRepository
{
    public function getCustomerCategories(Customer $customer)
    {
        return Category::query()
            ->select('name')
            ->where('customer_id', $customer->id)->get();
    }

    public function createCategory(Request $request, Customer $customer)
    {
        return Category::query()
            ->create([
                'name' => $request->name,
                'customer_id' => $customer->id
            ]);
    }

    public function updateCategory(Request $request, Category $category)
    {
        Category::query()->find($category->id)->update([
            'name' => $request->name,
        ]);

        return $category->refresh();
    }

     public function deleteCategory(Category $category)
    {
        return Category::query()->find($category->id)->delete();
    }
}
