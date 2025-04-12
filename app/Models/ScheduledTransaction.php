<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledTransaction extends Model
{
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
}
