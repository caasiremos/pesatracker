<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'customer_id',
        'wallet_id',
        'balance'
    ];

    /**
     * Get the customer that owns the wallet.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
