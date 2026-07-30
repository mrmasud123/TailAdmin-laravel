<?php

// app/Models/GoodsReceiptItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'date',
        'manufacture_date' => 'date',
        'quantity_received' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    protected $appends = ['subtotal'];

    public function getSubtotalAttribute(): float
    {
        return round($this->quantity_received * $this->unit_cost, 2);
    }

    public function goodsReceiptNote()
    {
        return $this->belongsTo(GoodsReceiptNote::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
