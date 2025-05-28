<?php

namespace App\Models\Relworx;

use App\Models\Merchant;
use App\Models\PriceList;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'code',
        'category',
        'has_price_list',
        'has_choice_list',
        'billable'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function merchants()
    {
        return $this->hasMany(Merchant::class);
    }

    public function priceList()
    {
        return $this->hasMany(PriceList::class);
    }
}
