<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('batch_no');
            $table->foreignId('goods_receipt_item_id')->nullable()->constrained('goods_receipt_items')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date');
            $table->decimal('quantity_available', 12, 2)->default(0);
            $table->decimal('unit_cost', 12, 2);
            $table->timestamps();

            // FEFO lookups will always filter by product_id then order by expiry_date
            $table->index(['product_id', 'expiry_date']);
            $table->unique(['product_id', 'batch_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
