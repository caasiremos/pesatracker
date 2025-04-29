<?php

namespace App\Payment\Relworx;

use App\Utils\Logger;
use Illuminate\Support\Facades\Http;


class Products
{
    private const PRODUCT_URL = "https://payments.relworx.com/api/products";

    public function getProducts()
    {
        $products = Http::asJson()->withHeaders(
            [
                'Accept' => 'application/vnd.relworx.v2',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('services.realworx.api_key')
            ],
        )->get(self::PRODUCT_URL)
            ->json();
        
        Logger::info(config('services.relworx.api_key'));
        Logger::info($products);
    }
}
