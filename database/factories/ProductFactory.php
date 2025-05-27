<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
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

        return $this->faker->randomElement($products);
    }
}
