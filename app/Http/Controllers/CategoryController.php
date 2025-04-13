<?php

namespace App\Http\Controllers;

use App\Http\Services\CategoryService;
use App\Models\Customer;
use Illuminate\Http\Request;
use Throwable;
use App\Exceptions\ExpectedException;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    public function index(Customer $customer)
    {
        try {
            $customer = $this->categoryService->getCategory($customer);
            return new ApiSuccessResponse($customer, "Success");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function store(Request $request, Customer $customer)
    {
        try {
            $customer = $this->categoryService->createCategory($request, $customer);
            return new ApiSuccessResponse($customer, "Category Created Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function update(Request $request, Customer $customer, Category $category)
    {
        try {
            $customer = $this->categoryService->updateCategory($request, $category);
            return new ApiSuccessResponse($customer, "Category Updated Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

     public function destroy(Customer $customer, Category $category)
    {
        try {
            $customer = $this->categoryService->deleteCategory($category, $customer);
            return new ApiSuccessResponse($customer, "Category Deleted Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }
}
