<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function index() {
        return view('admin.purchase.supplier-payments.index');
    }

    public function data() {}

    public function store(Request $request, PurchaseInvoice $invoice) {}

    public function history(Supplier $supplier) {
        return view('admin.purchase.history');
    }
}
