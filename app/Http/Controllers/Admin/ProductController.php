<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductType;
use App\Models\Supplier;
use App\Models\Unit;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{

    public function __construct(protected ProductService $productService){}
    public function products(){
        return view('admin.products.products',['title' => 'All Products']);
    }

    public function createProduct(){
        $brands = collect(Brand::all());

        $types = collect(ProductType::all());

        $categories = collect(Category::all());

        $suppliers = collect(Supplier::all());
        $title = "Add Product";
        return view('admin.products.form', compact('title','brands','types','categories','suppliers'));
    }

    public function editProduct(Product $product){
        $brands = collect(Brand::all());

        $types = collect(ProductType::all());

        $categories = collect(Category::all());

        $suppliers = collect(Supplier::all());
        $title= "Edit Product";
        return view('admin.products.form', compact('title','product', 'brands', 'types', 'categories', 'suppliers'));
    }

    public function storeProduct(StoreProductRequest $request){

        $data = $request->validated();

        $product=$this->productService->createProduct($data);

        if($request->hasFile('image')){
            $product->addMediaFromRequest('image')->toMediaCollection('products', 'public');
        }

        return redirect()->route('admin.products.manage')->with('success', 'Product created successfully.');
    }

    public function createProductStock(){}

    public function addStock(Product $product)
    {
        $title="Add Stock";
        $suppliers = Supplier::all();
        $units = Unit::all();
        return view('admin.products.add-stock', compact('product','title', 'suppliers', 'units'));
    }

    public function storeStock(StoreBatchProductRequest $request, $productId)
    {
        $this->productService->addToBatch($request->validated(), $productId);

        return redirect()
            ->route('admin.products.manage')
            ->with('success', 'Stock added successfully');
    }


    public function viewStock($productId)
    {
        $title= "View Stock";
        $product = Product::with('batches.supplier', 'batches.unit')->findOrFail($productId);

        return view('admin.products.view-stock', compact('product','title'));
    }

    public function expiryReport() {
        return view('admin.purchase.reports.expiry');
    }
    public function data()
    {
        return DataTables::of(
            Product::query()
                ->select([
                    'id',
                    'name',
                    // 'expire_date',
                    'brand_id',
                    'category_id',
                    // 'supplier_id',
                    'product_type_id'
                ])
                ->with(['brand', 'category', 'productType'])
            ->withCount('batches')
        )
    ->addColumn('batches_count', function($product){
        return $product->batches_count;
    })
        ->addColumn('brand', function ($product) {
            return $product->brand?->name ?? 'N/A';
        })

        ->addColumn('category', function ($product) {
            return $product->category?->name ?? 'N/A';
        })

        ->addColumn('supplier', function ($product) {
            return $product->supplier?->name ?? 'N/A';
        })

        ->addColumn('productType', function ($product) {
            return $product->productType?->name ?? 'N/A';
        })

        ->addColumn('action', function ($product) {
            return '

                <div class="flex justify-end items-center gap-2">

                    <a href="' . route('admin.product.edit', $product->id) . '"
                    class="flex items-center p-2 text-blue-600 hover:bg-blue-50 rounded-md transition"
                    title="Edit">
                        <span class="iconify me-1" data-icon="lucide:edit"></span>
                        Edit
                    </a>

                    <a href="' . route('admin.product.stock.create', $product->id) . '"
                    class="flex items-center p-2 text-emerald-600 hover:bg-emerald-50 rounded-md transition"
                    title="Add Stock">
                        <span class="iconify me-1" data-icon="lucide:package-plus"></span>Add Batch
                    </a>
                    <a href="' . route('admin.product.batches.view', $product->id) . '"
                    class="flex items-center p-2 text-blue-600 hover:bg-blue-50 rounded-md transition"
                    title="View Stock">
                        <span class="iconify me-1" data-icon="lucide:eye"></span>View Batches
                    </a>

                    <button class="flex items-center p-2 text-red-600 hover:bg-red-50 rounded-md transition deleteBtn"
                            data-id="' . $product->id . '"
                            title="Delete">
                        <span class="iconify me-1" data-icon="lucide:trash-2"></span>
                        Delete
                    </button>

                </div>
            ';
        })

        ->rawColumns(['action'])
        ->make(true);
    }
}
