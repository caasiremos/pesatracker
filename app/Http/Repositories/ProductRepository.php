<?php

namespace App\Http\Repositories;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Relworx\Product;
use App\Payment\Relworx\Products;
use Illuminate\Http\Request;

class ProductRepository
{
    public function __construct(private Products $products){ }

    /**
     * Get all products
     * 
     * @return array
     */
    public function getProducts()
    {
        return Product::query()
            ->with('priceList')
            ->where('category', '!=', 'BANK_TRANSFERS')
            //->where('has_price_list', 1)
            ->get();
    }

    /**
     * Get the price list for a product
     * 
     * @param string $code
     * @return array
     */
    public function getPriceList(string $code)
    {
        return $this->products->getPriceList($code);
    }

    /**
     * Get the choice list for a product
     * 
     * @param string $code
     * @return array
     */
    public function getChoiceList(string $code)
    {
        return $this->products->getChoiceList($code);
    }
}
