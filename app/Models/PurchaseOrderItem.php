<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $table= 'purchase_order_items';
    protected $guarded= [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
