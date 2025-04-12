<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'customer_id'
    ];

    /**
     * Get the customer that owns the merchant.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
