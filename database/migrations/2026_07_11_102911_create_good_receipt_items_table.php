<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_note_id')->constrained('goods_receipt_notes')->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained('purchase_order_items')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('batch_no');
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date');
            $table->decimal('quantity_received', 12, 2);
            $table->decimal('unit_cost', 12, 2);
            $table->timestamps();

            $table->index(['product_id', 'batch_no']);
            $table->index(['expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_items');
    }
};
