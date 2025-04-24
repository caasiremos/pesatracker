<?php

namespace App\Models;

use App\Utils\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'category_id',
        'amount',
        'payment_date',
        'note',
        'attachment',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getPaymentDateAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }
}
