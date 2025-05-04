<?php

namespace App\Models\Relworx;

use App\Models\Merchant;
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

    public function merchants()
    {
        return $this->hasMany(Merchant::class);
    }
}
