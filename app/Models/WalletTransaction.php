<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_id',
        'wallet_id',
        'amount',
        'type',
        'provider',
        'transaction_phone_number',
        'transaction_reference',
        'transaction_status',
        'telecom_product'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
