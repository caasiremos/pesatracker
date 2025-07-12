<?php

namespace App\Models;

use App\Models\Relworx\Product;
use App\Utils\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTransaction extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'customer_id',
        'category_id',
        'product_id',
        'code',
        'amount',
        'payment_date',
        'frequency',
        'note',
        'transaction_phone_number',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'integer',
    ];

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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
