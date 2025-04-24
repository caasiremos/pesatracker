<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'message'
    ];

    /**
     * Get the customer that owns the feedback.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
}
