<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Repositories\ProductRepository;
use Throwable;

class ProductService
{
    public function __construct(private ProductRepository $productRepository) {}

    /**
     * Get all products
     * 
     * @return array
     */
    public function getProducts()
    {
        return $this->productRepository->getProducts();
    }

    /**
     * Get the price list for a product
     * 
     * @param string $code
     * @return array
     */
    public function getPriceList(string $code)
    {
        try {
            return $this->productRepository->getPriceList($code);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * Get the choice list for a product
     * 
     * @param string $code
     * @return array
     */
    public function getChoiceList(string $code)
    {
        try {
            return $this->productRepository->getChoiceList($code);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}
