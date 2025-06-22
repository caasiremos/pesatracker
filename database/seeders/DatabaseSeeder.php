<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\CashExpense;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Feedback;
use App\Models\Merchant;
use App\Models\PriceList;
use App\Models\Relworx\Product;
use App\Models\ScheduledTransaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Payment\Relworx\Products;
use Database\Factories\ScheduledTransactionFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PHPUnit\TextUI\Configuration\Merger;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'Aremo Isaac',
        //     'email' => 'aremoisaac@gmail.com',
        // ]);

        $this->createProducts();
        $this->createPriceList();
        // Customer::factory(100)->create();
        // Category::factory(100)->create();
        // Merchant::factory(100)->create();
        // Budget::factory(100)->create();
        // ScheduledTransaction::factory(100)->create();
        // CashExpense::factory(100)->create();
        // Feedback::factory(100)->create();
        // Wallet::factory(100)->create();
        // WalletTransaction::factory(100)->create();
    }

    private function products()
    {
        return [
            [
                'name' => 'GOtv - Multichoice',
                'code' => 'GO_TV',
                'category' => 'TV',
                'has_price_list' => 1,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'Uganda Telecom Airtime',
                'code' => 'UTL_AIRTIME',
                'category' => 'AIRTIME',
                'has_price_list' => 0,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'MTN Uganda Airtime',
                'code' => 'MTN_UG_AIRTIME',
                'category' => 'AIRTIME',
                'has_price_list' => 0,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'AZAM TV',
                'code' => 'AZAM_TV',
                'category' => 'TV',
                'has_price_list' => 1,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'Airtel Uganda Internet',
                'code' => 'AIRTEL_UG_INTERNET',
                'category' => 'INTERNET',
                'has_price_list' => 1,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'Roke Telecom Internet',
                'code' => 'ROKE_TELECOM_UG_INTERNET',
                'category' => 'INTERNET',
                'has_price_list' => 1,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'MTN Uganda Internet',
                'code' => 'MTN_UG_INTERNET',
                'category' => 'INTERNET',
                'has_price_list' => 1,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'Airtel Uganda Voice Bundles',
                'code' => 'AIRTEL_UG_VOICE_BUNDLES',
                'category' => 'OTHERS',
                'has_price_list' => 1,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'DSTV - Multichoice',
                'code' => 'DSTV',
                'category' => 'TV',
                'has_price_list' => 1,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'MTN Uganda Voice Bundles',
                'code' => 'MTN_UG_VOICE_BUNDLES',
                'category' => 'OTHERS',
                'has_price_list' => 1,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'UECDL Light',
                'code' => 'UMEME_PRE_PAID',
                'category' => 'UTILITIES',
                'has_price_list' => 0,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'National Water',
                'code' => 'NATIONUMEME_PRE_PAID',
                'category' => 'UTILITIES',
                'has_price_list' => 0,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'Startimes Bouquets',
                'code' => 'STARTIMES',
                'category' => 'TV',
                'has_price_list' => 1,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'Airtel Uganda Airtime',
                'code' => 'AIRTEL_UG_AIRTIME',
                'category' => 'AIRTIME',
                'has_price_list' => 0,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
            [
                'name' => 'Multichoice - DSTV/GOtv',
                'code' => 'MULTICHOICE',
                'category' => 'TV',
                'has_price_list' => 1,
                'has_choice_list' => 0,
                'billable' => 1,
            ],
        ];
    }

    private function createProducts()
    {
        foreach ($this->products() as $product) {
            Product::create($product);
        }
    }

    private function createPriceList()
    {
        $products = Product::where('has_price_list', 1)->get();

        foreach ($products as $product) {
            echo $product->code . "\n";
            $priceList = (new Products())->getPriceList($product->code);
            if ($priceList['success'] == true) {
                foreach ($priceList['price_list'] as $price) {
                    PriceList::updateOrCreate([
                        'product_id' => $product->id,
                        'code' => $price['code'],
                    ], [
                        'price' => $price['price'],
                        "name" => $price['name'],
                    ]);
                }
            }
        }
    }
}
