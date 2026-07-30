<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $guarded=[];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }
}
