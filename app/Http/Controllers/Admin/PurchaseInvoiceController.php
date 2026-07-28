<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function index() {
        return view('admin.purchase.purchase-invoices.index');
    }

    public function data() {}

    public function show(PurchaseInvoice $invoice) {
        return view('admin.purchase.purchase-invoices.show', compact('invoice'));
    }

    public function edit(PurchaseInvoice $invoice) {
        return view('admin.purchase.purchase-invoices.edit');
    }

    public function update(Request $request, PurchaseInvoice $invoice) {}
}
