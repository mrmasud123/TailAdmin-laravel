<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceiptNote;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    public function index() {
        return view('admin.purchase.goods-receipts.index');
    }

    public function data() {}

    public function create(PurchaseOrder $purchaseOrder) {
        return view('admin.purchase.goods-receipts.create', compact('purchaseOrder'));
    }

    public function store(Request $request) {}

    public function show(GoodsReceiptNote $grn) {
        return view('admin.purchase.purchase-returns.show', compact('grn'));
    }

    public function edit(GoodsReceiptNote $grn) {}

    public function update(Request $request, GoodsReceiptNote $grn) {}

    public function getPendingItems(PurchaseOrder $purchaseOrder) {}

    public function getBatches(GoodsReceiptNote $grn) {}
}
