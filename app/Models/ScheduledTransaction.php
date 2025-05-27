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
        'merchant_id',
        'amount',
        'payment_date',
        'frequency',
        'reference',
        'note',
        'transaction_phone_number',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'integer',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->reference = 'REF-' . str_pad(self::count() + 1, 6, '0', STR_PAD_LEFT);
        });
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
