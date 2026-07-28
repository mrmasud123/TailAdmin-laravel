<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function dashboard() {
        return view('admin.purchase.dashboard');
    }

    public function index() {
        return view('admin.purchase.index');
    }

    public function data() {}

    public function create() {
        $suppliers=Supplier::all();
        return view('admin.purchase.create', compact('suppliers'));
    }

    public function store(Request $request) {}

    public function edit(PurchaseOrder $purchaseOrder) {
        return view('admin.purchase.edit');
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder) {}

    public function destroy(PurchaseOrder $purchaseOrder) {}

    public function print(PurchaseOrder $purchaseOrder) {
        return view('admin.purchase.print');
    }

    public function pendingApproval() {
        return view('admin.purchase.pending-approval');
    }

    public function approve(PurchaseOrder $purchaseOrder) {}

    public function reject(PurchaseOrder $purchaseOrder) {}

    public function bySupplierReport() {}
}
