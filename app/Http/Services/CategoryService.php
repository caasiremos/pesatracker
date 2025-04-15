<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Repositories\CategoryRepository;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Throwable;

class CategoryService
{
    public function __construct(private CategoryRepository $category) {}

    public function getCategory(Customer $customer)
    {
        return $this->category->getCustomerCategories($customer);
    }

    public function createCategory(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);

            return $this->category->createCategory($request, $customer);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    public function updateCategory(Request $request, Category $category)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);

            return $this->category->updateCategory($request, $category);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    public function deleteCategory(Category $category, Customer $customer)
    {
        try {
            $category = Category::query()
                ->where('customer_id', $customer->id)
                ->where('id', $category->id)
                ->first();

            if(!$category){
                throw new ExpectedException("Category not found or does not belong to customer");
            }

            return $this->category->deleteCategory($category);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}
