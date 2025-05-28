<?php

namespace App\Models;

use App\Models\Relworx\Product;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $fillable = [
        'product_id',
        'code',
        'name',
        'price',
    ];

      protected $hidden = ['created_at', 'updated_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
