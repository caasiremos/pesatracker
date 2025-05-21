<?php

namespace App\Payment\Relworx;

use App\Models\Relworx\Product;
use App\Utils\Logger;
use Illuminate\Support\Facades\Http;


class Products
{
    private const PRODUCT_URL = "https://payments.relworx.com/api/products";
    private const PRICE_LIST_URL = "https://payments.relworx.com/api/products/price-list";
    private const CHOICE_LIST_URL = "https://payments.relworx.com/api/products/choice-list";
    private const VALIDATE_PRODUCT_URL = "https://payments.relworx.com/api/products/validate";
    public string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.relworx.api_key');
    }

    /**
     * Get all products from Relworx
     * 
     * @return array
     */
    public function getProducts()
    {
        $response = Http::asJson()->withHeaders(
            [
                'Accept' => 'application/vnd.relworx.v2',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey
            ],
        )->get(self::PRODUCT_URL)
            ->json();
        
        Logger::info($response);

        if (isset($response['products']) && is_array($response['products'])) {
            foreach ($response['products'] as $product) {
                Product::updateOrCreate(
                    [
                        'code' => $product['code'],
                    ],
                    [
                        'name' => $product['name'],
                        'category' => $product['category'],
                        'has_price_list' => $product['has_price_list'],
                        'has_choice_list' => $product['has_choice_list'],
                        'billable' => $product['billable']
                    ]
                );
            }
        }
    }

    /**
     * Get the price list for a product
     * 
     * @param string $code
     * @return array
     */
    public function getPriceList(string $code)
    {
        $response = Http::asJson()->withHeaders(
            [
                'Accept' => 'application/vnd.relworx.v2',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey
            ],
        )->get(self::PRICE_LIST_URL . '?code=' . $code)
            ->json();

        Logger::info($response);
        
        return $response;
    }

    /**
     * Get the choice list for a product
     * 
     * @param string $code
     * @return array
     */
    public function getChoiceList(string $code)
    {
        $response = Http::asJson()->withHeaders(
            [
                'Accept' => 'application/vnd.relworx.v2',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey
            ],
        )->get(self::CHOICE_LIST_URL . '?code=' . $code)
            ->json();

        Logger::info($response);

        return $response;
    }

    public function validateProduct(string $accountNumber, string $reference, string $msisdn, int $amount, string $productCode, string $contactPhone)
    {
        $response = Http::asJson()->withHeaders(
            [
                'Accept' => 'application/vnd.relworx.v2',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey
            ],
        )->post(self::VALIDATE_PRODUCT_URL, [
            'account_no' => $accountNumber,
            'reference' => $reference,
            'msisdn' => $msisdn,
            'amount' => $amount,
            'product_code' => $productCode,
            'contact_phone' => $contactPhone
        ])->json();

        Logger::info($response);
        return $response;
    }
}