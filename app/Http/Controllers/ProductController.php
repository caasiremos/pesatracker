<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Throwable;
use App\Exceptions\ExpectedException;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Services\ProductService;
use App\Models\Merchant;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    /**
     * Get all products
     * 
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function getProducts(): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $products = $this->productService->getProducts();
            return new ApiSuccessResponse($products, "Products Fetched Successful");
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    /**
     * Get the price list for a product
     * 
     * @param Request $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function getPriceList(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $priceList = $this->productService->getPriceList($request->code);
            return new ApiSuccessResponse($priceList, "Price List Fetched Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    /**
     * Get the choice list for a product
     * 
     * @param Request $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function getChoiceList(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $choiceList = $this->productService->getChoiceList($request->code);
            return new ApiSuccessResponse($choiceList, "Choice List Fetched Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }
}
