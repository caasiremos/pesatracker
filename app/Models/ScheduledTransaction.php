<?php

namespace App\Models;

use App\Utils\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'category_id',
        'merchant_id',
        'amount',
        'payment_date',
        'frequency',
        'reference',
        'note',
        'provider',
        'transaction_phone_number',
        'transaction_reference',
        'transaction_status',
        'telecom_product'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'integer',
    ];

    public function getAmountAttribute($value)
    {
        return Money::formatAmount($value);
    }

    public function getPaymentDateAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
