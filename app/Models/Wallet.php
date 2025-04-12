<?php

namespace App\Models;

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

        static::creating(function ($model) {
            $model->wallet_identifier = 'PTW-' . str_pad(self::max('id') + 1, 5, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Get the customer that owns the wallet.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
