<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceiptNote extends Model
{
    use HasFactory,SoftDeletes;
    protected $table= "goods_receipt_notes";
    protected $guarded=[];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class, 'goods_receipt_note_id');
    }
}
