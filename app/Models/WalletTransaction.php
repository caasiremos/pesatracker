<?php

namespace App\Models;

use App\Utils\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'wallet_id',
        'amount',
        'provider',
        'transaction_phone_number',
        'transaction_reference',
        'transaction_status',
        'telecom_product'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->transaction_status = 'pending';
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function getAmountAttribute($value)
    {
        return Money::formatAmount($value);
    }
}
