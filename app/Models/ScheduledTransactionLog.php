<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTransactionLog extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'scheduled_transaction_id',
        'status',
        'amount',
        'fee',
        'scheduled_date',
        'internal_transaction_reference',
        'external_transaction_reference',
        'telco_provider',
        'provider_fee',
        'external_status',
        'error_message',
    ];

    public function scheduledTransaction()
    {
        return $this->belongsTo(ScheduledTransaction::class);
    }
}
