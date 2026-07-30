<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';
    protected $guarded = [];
    protected $casts = [
        'expected_delivery_date' => 'date',
        'order_date' => 'date',
    ];


    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
