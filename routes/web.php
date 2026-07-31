<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\GoodsReceiptController;
use App\Http\Controllers\Admin\PermisssionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\PurchaseInvoiceController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\PurchaseReturnController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\SupplierPaymentController;
use App\Http\Controllers\Admin\SuppliersController;
use App\Http\Controllers\Admin\UnitsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AIChatbotController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\DueCollectionController;
use App\Http\Controllers\SsoCallbackController;
use App\Http\Middleware\TrySsoSilentLogin;

Route::get('/sso/callback', [SsoCallbackController::class, 'callback'])
    ->name('sso.callback')
    ->withoutMiddleware(['auth']);

Route::get('/login', [AuthController::class, 'index'])->name('login.index');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/auth/google/redirect', [AuthController::class, 'googleRedirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');

//Route::middleware([TrySsoSilentLogin::class])->group(function () {
    Route::get('/', function () {
        if (!auth()->check()) {
            return redirect('/login');
        }

        return view('pages.dashboard.ecommerce');
    })->name('dashboard');
//});

Route::middleware(['auth:web'])->group(function () {
    Route::get('/online-users', [AdminController::class, 'onlineUsers'])->name('admin.online-users');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/roles', [RolesController::class, 'index'])->name('admin.roles');
    Route::get('/roles/create', [RolesController::class, 'create'])->name('admin.roles.create');
    Route::post('/roles', [RolesController::class, 'store'])->name('admin.roles.store');
    Route::get('/roles/{role}/permissions', [RolesController::class, 'show'])->name('admin.add.permissions.to.role');
    Route::put('/roles/{role}/permissions', [RolesController::class, 'assignPermission'])->name('admin.roles.update-permissions');
    Route::get('/admin/roles/data', [RolesController::class, 'data'])->name('admin.roles.data');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('readAll');
    });

    Route::get('/permissions', [PermisssionController::class, 'index'])->name('admin.permissions');
    Route::get('/permissions/create', [PermisssionController::class, 'create'])->name('admin.permissions.create');
    Route::post('/permissions', [PermisssionController::class, 'store'])->name('admin.permissions.store');

    // Sales
    Route::get('/sales', [SalesController::class, 'index'])->name('admin.sales.index');
    Route::get('/sales/data', [SalesController::class, 'data'])->name('admin.sales.data');
    Route::get('/sales-create', [SalesController::class, 'create'])->name('admin.sales.create');
    Route::post('/sales', [SalesController::class, 'store'])->name('admin.sales.store');
    Route::get('/sales/{sale}', [SalesController::class, 'show'])->name('admin.sales.show');
    Route::get('/sales/{sale}/edit', [SalesController::class, 'edit'])->name('admin.sales.edit');
    Route::put('/sales/{sale}', [SalesController::class, 'update'])->name('admin.sales.update');
    Route::delete('/sales/{sale}', [SalesController::class, 'destroy'])->name('admin.sales.destroy');
    Route::get('/sales/invoice/{id}', [SalesController::class, 'invoices'])->name('admin.sales.invoice');

    // Products (lookup helpers used by Sales)
    Route::get('/get-products', [SalesController::class, 'getProducts']);
    Route::get('/get-product-batches/{id}', [SalesController::class, 'getBatches']);

    // Suppliers
    Route::get('/suppliers', [SuppliersController::class, 'manageSuppliers'])->name('admin.suppliers.manage');
    Route::get('/suppliers/data', [SuppliersController::class, 'data'])->name('admin.suppliers.data');
    Route::get('/suppliers/create', [SuppliersController::class, 'createSuppliers'])->name('admin.supplier.create');
    Route::post('/suppliers', [SuppliersController::class, 'storeSuppliers'])->name('admin.supplier.store');
    Route::get('/suppliers/{supplier}/edit', [SuppliersController::class, 'editSuppliers'])->name('admin.suppliers.edit');
    Route::put('/suppliers/{supplier}', [SuppliersController::class, 'updateSuppliers'])->name('admin.suppliers.update');
    Route::delete('/suppliers/{supplier}', [SuppliersController::class, 'deleteSuppliers'])->name('admin.suppliers.destroy');
    Route::post('/admin/supplier/status/{id}', [SuppliersController::class, 'updateStatus']);

    // Brands
    Route::get('/brands', [BrandController::class, 'brands'])->name('admin.brands.manage');
    Route::get('/brands/data', [BrandController::class, 'data'])->name('admin.brands.data');
    Route::get('/brands/create', [BrandController::class, 'createBrand'])->name('admin.brand.create');
    Route::get('/brands/{brand}/edit', [BrandController::class, 'editBrand'])->name('admin.brand.edit');
    Route::delete('/brands/{brand}', [BrandController::class, 'deleteBrand'])->name('admin.brand.destroy');
    Route::post('/brands', [BrandController::class, 'store'])->name('admin.brand.store');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('admin.brand.update');
    Route::post('/admin/brand/status/{id}', [BrandController::class, 'updateStatus'])->name('admin.brand.status.update');

    // Units
    Route::get('/units', [UnitsController::class, 'units'])->name('admin.units.manage');
    Route::post('/units', [UnitsController::class, 'store'])->name('admin.unit.store');
    Route::put('/units/{unit}', [UnitsController::class, 'update'])->name('admin.unit.update');
    Route::get('/units/data', [UnitsController::class, 'data'])->name('admin.units.data');
    Route::get('/units/create', [UnitsController::class, 'createUnit'])->name('admin.unit.create');
    Route::get('/units/{unit}/edit', [UnitsController::class, 'editUnit'])->name('admin.unit.edit');
    Route::delete('/units/{unit}', [UnitsController::class, 'deleteUnit'])->name('admin.unit.destroy');
    Route::post('/admin/unit/status/{id}', [UnitsController::class, 'updateStatus'])->name('admin.unit.status.update');

    // Product types
    Route::get('/product-types', [ProductTypeController::class, 'productTypes'])->name('admin.product.type.manage');
    Route::get('/product-types/data', [ProductTypeController::class, 'data'])->name('admin.product.types.data');
    Route::get('/product-types/create', [ProductTypeController::class, 'createProductType'])->name('admin.product.type.create');
    Route::get('/product-types/{productType}/edit', [ProductTypeController::class, 'editProductType'])->name('admin.product.type.edit');
    Route::delete('/product-types/{productType}', [ProductTypeController::class, 'deleteProductType'])->name('admin.product.type.destroy')->whereNumber('productType');
    Route::post('/product-types', [ProductTypeController::class, 'storeProductType'])->name('admin.product.type.store');
    Route::put('/product-types/{productType}', [ProductTypeController::class, 'updateProductType'])->name('admin.product.type.update');
    Route::post('/admin/product-type/status/{id}', [ProductTypeController::class, 'updateStatus']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'categories'])->name('admin.categories.manage');
    Route::get('/categories/create', [CategoryController::class, 'createCategory'])->name('admin.categories.create');
    Route::post('/categories', [CategoryController::class, 'storeCategory'])->name('admin.category.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'editCategory'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'updateCategory'])->name('admin.category.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'deleteCategory'])->name('admin.categories.destroy');
    Route::get('/admin/category/data', [CategoryController::class, 'data'])->name('admin.category.data');
    Route::post('/admin/category/status/{id}', [CategoryController::class, 'updateStatus']);

    // Customers
    Route::get('/customers', [CustomerController::class, 'customers'])->name('admin.customers.manage');

    Route::get('/role-permission-mapping', [CustomerController::class, 'rolePermissionMapping'])
        ->name('role.permission.mapping')->middleware('role:admin');

    Route::post('/role-permission-mapping/store', [CustomerController::class, 'storeMapping'])
        ->name('role.permission.mapping.store');

    Route::get('/users/with/roles/permissions/data', [CustomerController::class, 'userWithRolesPermissionData'])->name('admin.customers.with.roles.permissions.data');
    Route::get('/role-permission-mapping/map/{user}', [CustomerController::class, 'assignEmployeeRole'])->name('admin.assign.role');

    Route::get('/customers/create', [CustomerController::class, 'createCustomer'])->name('admin.customers.create');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'editCustomer'])->name('admin.customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'updateCustomer'])->name('admin.customer.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'deleteCustomer'])->name('admin.customers.destroy');
    Route::get('/admin/customer/data', [CustomerController::class, 'data'])->name('admin.customer.data');
    Route::post('/admin/customer/status/{id}', [CustomerController::class, 'updateStatus']);

    Route::get('/customers/search', [CustomerController::class, 'search'])->name('admin.customers.search');
    Route::get('/customers/invoice', [CustomerController::class, 'invoice'])->name('admin.customers.invoice');
    Route::post('/customers/store', [CustomerController::class, 'store'])->name('admin.customers.store');

    // Products
    Route::get('/products', [ProductController::class, 'products'])->name('admin.products.manage');
    Route::get('/products/data', [ProductController::class, 'data'])->name('admin.products.data');
    Route::get('/products/create', [ProductController::class, 'createProduct'])->name('admin.product.create');
    Route::get('/products/{product}/edit', [ProductController::class, 'editProduct'])->name('admin.product.edit');
    Route::get('/products/delete', [ProductController::class, 'deleteProduct'])->name('admin.product.destroy');
    Route::post('/products', [ProductController::class, 'storeProduct'])->name('admin.product.store');
    Route::put('/products/{product}', [ProductController::class, 'updateProduct'])->name('admin.product.update');
    Route::get('/products/stock/{product}/create', [ProductController::class, 'addStock'])->name('admin.product.stock.create');
    Route::get('/products/{product}/batches', [ProductController::class, 'viewStock'])->name('admin.product.batches.view');
    Route::post('/products/stock/{product}/store', [ProductController::class, 'storeStock'])->name('admin.product.stock.store');

    // AI chat bot
    Route::get('/ai-chat', [AIChatbotController::class, 'index'])->name('admin.ai-chat.index');
    Route::post('/continue-chat', [AIChatbotController::class, 'continueChat'])->name('admin.ai-chat.continue');

    // Profile
    Route::get('/profile', [AdminController::class, 'index'])->name('profile');

    // Due collection
    Route::get('/collections', [DueCollectionController::class, 'index'])->name('admin.due.collections');
    Route::get('/due-collections/data', [DueCollectionController::class, 'data'])->name('admin.due.collection.data');
    Route::get('/collections/customers-invoices/{customer}', [DueCollectionController::class, 'customerInvoices'])->name('admin.collections.customers.invoices');
    Route::post('invoice/{sale}/pay-due', [DueCollectionController::class, 'payDue'])->name('admin.invoice.sales.pay-due');
    Route::get('/collections/invoice/{sale}/edit', [DueCollectionController::class, 'edit'])->name('admin.invoice.edit');
    Route::put('sales/{sale}', [DueCollectionController::class, 'update'])->name('admin.sales.update');
    Route::get('/collections/payment-history/{customer}', [DueCollectionController::class, 'paymentHistory'])->name('admin.collections.payment.history');

    // Purchase Dashboard
    Route::get('/purchase-dashboard', [PurchaseOrderController::class, 'dashboard'])->name('admin.purchase.dashboard');

    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('admin.purchase-orders.manage');
    Route::get('/purchase-orders/data', [PurchaseOrderController::class, 'data'])->name('admin.purchase-orders.data');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('admin.purchase-order.create');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('admin.purchase-order.store');
    Route::get('/purchase-orders/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('admin.purchase-order.edit');
    Route::put('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('admin.purchase-order.update');
    Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('admin.purchase-order.destroy');
    Route::get('/purchase-orders/{purchaseOrder}/print', [PurchaseOrderController::class, 'print'])->name('admin.purchase-order.print');

    Route::get('/purchase-orders/pending-approval', [PurchaseOrderController::class, 'pendingApproval'])->name('admin.purchase-order.pending');
    Route::post('/purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('admin.purchase-order.approve');
    Route::post('/purchase-orders/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])->name('admin.purchase-order.reject');

    Route::get('/goods-receipts', [GoodsReceiptController::class, 'index'])->name('admin.grn.manage');
    Route::get('/goods-receipts/data', [GoodsReceiptController::class, 'data'])->name('admin.grn.data');
    Route::get('/goods-receipts/create/{purchaseOrder}', [GoodsReceiptController::class, 'create'])->name('admin.grn.create');
    Route::post('/goods-receipts', [GoodsReceiptController::class, 'store'])->name('admin.grn.store');
    Route::get('/goods-receipts/{grn}', [GoodsReceiptController::class, 'show'])->name('admin.grn.show');
    Route::get('/goods-receipts/{grn}/edit', [GoodsReceiptController::class, 'edit'])->name('admin.grn.edit');
    Route::put('/goods-receipts/{grn}', [GoodsReceiptController::class, 'update'])->name('admin.grn.update');

    Route::get('/get-po-items/{purchaseOrder}', [GoodsReceiptController::class, 'getPendingItems']);

    Route::get('/purchase-returns', [PurchaseReturnController::class, 'index'])->name('admin.purchase-returns.manage');
    Route::get('/purchase-returns/data', [PurchaseReturnController::class, 'data'])->name('admin.purchase-returns.data');
    Route::get('/purchase-returns/create', [PurchaseReturnController::class, 'create'])->name('admin.purchase-return.create');
    Route::post('/purchase-returns', [PurchaseReturnController::class, 'store'])->name('admin.purchase-return.store');
    Route::get('/purchase-returns/{purchaseReturn}', [PurchaseReturnController::class, 'show'])->name('admin.purchase-return.show');

    Route::get('/get-grn-batches/{grn}', [GoodsReceiptController::class, 'getBatches']);

    Route::get('/purchase-invoices', [PurchaseInvoiceController::class, 'index'])->name('admin.purchase-invoices.manage');
    Route::get('/purchase-invoices/data', [PurchaseInvoiceController::class, 'data'])->name('admin.purchase-invoices.data');
    Route::get('/purchase-invoices/{invoice}', [PurchaseInvoiceController::class, 'show'])->name('admin.purchase-invoice.show');
    Route::get('/purchase-invoices/{invoice}/edit', [PurchaseInvoiceController::class, 'edit'])->name('admin.purchase-invoice.edit');
    Route::put('/purchase-invoices/{invoice}', [PurchaseInvoiceController::class, 'update'])->name('admin.purchase-invoice.update');

    Route::get('/supplier-payments', [SupplierPaymentController::class, 'index'])->name('admin.supplier-payments.manage');
    Route::get('/supplier-payments/data', [SupplierPaymentController::class, 'data'])->name('admin.supplier-payments.data');
    Route::post('/purchase-invoices/{invoice}/pay', [SupplierPaymentController::class, 'store'])->name('admin.supplier-payment.store');
    Route::get('/supplier-payments/history/{supplier}', [SupplierPaymentController::class, 'history'])->name('admin.supplier-payment.history');

    Route::get('/reports/expiry', [ProductController::class, 'expiryReport'])->name('admin.reports.expiry');
    Route::get('/reports/purchase-by-supplier', [PurchaseOrderController::class, 'bySupplierReport'])->name('admin.reports.purchase-by-supplier');
});

// calendar pages
Route::get('/calendar', function () {
    return view('pages.calender', ['title' => 'Calendar']);
})->name('calendar');

// form pages
Route::get('/form-elements', function () {
    return view('pages.form.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

// tables pages
Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

// pages
Route::get('/blank', function () {
    return view('pages.blank', ['title' => 'Blank']);
})->name('blank');

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');

// authentication pages
Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');

// ui elements pages
Route::get('/alerts', function () {
    return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
})->name('alerts');

Route::get('/avatars', function () {
    return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
})->name('avatars');

Route::get('/badge', function () {
    return view('pages.ui-elements.badges', ['title' => 'Badges']);
})->name('badges');

Route::get('/buttons', function () {
    return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
})->name('buttons');

Route::get('/image', function () {
    return view('pages.ui-elements.images', ['title' => 'Images']);
})->name('images');

Route::get('/videos', function () {
    return view('pages.ui-elements.videos', ['title' => 'Videos']);
})->name('videos');
