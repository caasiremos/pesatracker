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

    /**
     * Get categories with spent amount
     * 
     * @param Customer $customer
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function getCategoriesWithSpentAmount(Customer $customer): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->categoryService->getCategoriesWithSpentAmount($customer);
            return new ApiSuccessResponse($customer, "Success");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }


    /**
     * Get all categories for a customer
     * 
     * @param Customer $customer
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(Customer $customer): ApiSuccessResponse|ApiErrorResponse
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

    /**
     * Create a category
     * 
     * @param Request $request
     * @param Customer $customer
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(Request $request, Customer $customer): ApiSuccessResponse|ApiErrorResponse
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

    /**
     * Update a category
     * 
     * @param Request $request
     * @param Customer $customer
     * @param Category $category
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request, Category $category): ApiSuccessResponse|ApiErrorResponse
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

    /**
     * Delete a category
     * 
     * @param Customer $customer
     * @param Category $category
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy(Customer $customer, Category $category): ApiSuccessResponse|ApiErrorResponse
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
