<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReturn;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    public function index() {
        return view('admin.purchase.purchase-returns.index');
    }

    public function data() {}

    public function create() {
        return view('admin.purchase.purchase-returns.create');
    }

    public function store(Request $request) {}

    public function show(PurchaseReturn $purchaseReturn) {
        return view('admin.purchase.purchase-returns.show', compact('purchaseReturn'));
    }
}
