<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    protected $fillable = [
        'customer_id',
        'supplier_id',
        'transactionable_id',
        'transactionable_type',
        'type',
        'direction',
        'amount',
        'balance_after',
        'payment_method',
        'reference_no',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getSignedAmountAttribute(): float
    {
        return $this->direction === 'credit'
            ? (float) $this->amount
            : -(float) $this->amount;
    }
}
