<?php

namespace App\Models;

use App\Utils\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'wallet_identifier',
        'balance'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($wallet) {
            $wallet->wallet_identifier = 'PTW-' . str_pad(self::max('id') + 1, 5, '0', STR_PAD_LEFT);
            $wallet->balance = 0;
        });
    }

    public function getBalanceAttribute($value)
    {
        return Money::formatAmount($value);
    }

    /**
     * Get the customer that owns the wallet.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
