<?php

namespace App\Models;

use App\Utils\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLog extends Model
{
    const STATUS_PENDING = 'pending';

    const STATUS_FAILED = 'failed';

    const STATUS_SUCCESS = 'success';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($transaction_log) {
            $transaction_log->status = self::STATUS_PENDING;
        });
    }

    public function getAmountAttribute($value)
    {
        return Money::formatAmount($value);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
