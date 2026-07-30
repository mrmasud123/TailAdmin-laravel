<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGoodsReceiptRequest;
//use App\Models\GoodsReceiptNote;
//use App\Models\PurchaseOrder;
//use App\Models\Supplier;
use App\Services\GoodsReceiptService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\{GoodsReceiptNote, PurchaseOrder, Supplier};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class GoodsReceiptController extends Controller
{
    public function __construct(protected GoodsReceiptService $goodsReceiptService)
    {
    }
    public function index() {
        $suppliers= Supplier::all();
        return view('admin.purchase.goods-receipts.index', compact('suppliers'));
    }



    public function create(PurchaseOrder $purchaseOrder) {
        $suppliers= Supplier::all();
        $purchaseOrder->load([
            'supplier',
            'items.product',
            'items.product:id,name'
        ]);
        return view('admin.purchase.goods-receipts.create', compact('purchaseOrder','suppliers'));
    }
    public function store(StoreGoodsReceiptRequest $request)
    {

        try {
            $grn = $this->goodsReceiptService->store(
                $request->validated(),
                $request->user()->id
            );

            return response()->json([
                'message' => $grn->status === 'completed'
                    ? "Goods receipt {$grn->grn_number} confirmed and stock updated."
                    : "Goods receipt {$grn->grn_number} saved as pending.",
                'data' => $grn->load('items'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('GRN store failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'message' => 'Something went wrong while saving the goods receipt.',
            ], 500);
        }
    }

    public function show(GoodsReceiptNote $grn)
    {
        $grn->load([
            'purchaseOrder',
            'supplier',
            'receiver:id,name',
            'items.purchaseOrderItem:id,quantity_ordered,quantity_received',
            'items.product:id,name',
            'purchaseReturns' => fn ($q) => $q->latest('return_date'),
        ]);

        return view('admin.purchase.goods-receipts.show', compact('grn'));
    }

    public function edit(GoodsReceiptNote $grn) {}

    public function update(Request $request, GoodsReceiptNote $grn) {}

    public function getPendingItems(PurchaseOrder $purchaseOrder) {}

    public function getBatches(GoodsReceiptNote $grn) {}

    public function data(Request $request)
    {
        return DataTables::of(
            GoodsReceiptNote::query()
                ->select([
                    'id',
                    'grn_number',
                    'purchase_order_id',
                    'supplier_id',
                    'received_date',
                    'status',
                ])
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->filled('supplier_id'), function ($query) use ($request) {
                    $query->where('supplier_id', $request->supplier_id);
                })
                ->when($request->filled('received_date'), function ($query) use ($request) {
                    $query->whereDate('received_date', $request->received_date);
                })
                ->with(['supplier', 'purchaseOrder'])
                ->withCount('items')
        )
            ->editColumn('grn_number', function ($grn) {
                return '
                <div class="flex flex-col gap-1">
                    <span class="font-semibold text-slate-800 dark:text-slate-100 tracking-tight">
                        ' . e($grn->grn_number) . '
                    </span>
                    <span class="inline-flex items-center gap-1 w-fit px-2 py-0.5 rounded-full text-[11px] font-medium
                        bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100
                        dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20">
                        <span class="iconify" data-icon="lucide:package" style="font-size:11px"></span>
                        ' . $grn->items_count . ' ' . Str::plural('item', $grn->items_count) . '
                    </span>
                </div>
            ';
            })

            ->addColumn('supplier', function ($grn) {
                return $grn->supplier?->name ?? 'N/A';
            })

//            ->addColumn('po_number', function ($grn) {
//                return $grn->purchaseOrder?->po_number ?? 'N/A';
//            })

            ->editColumn('received_date', function ($grn) {
                return Carbon::parse($grn->received_date)->format('Y-m-d');
            })
            ->editColumn('sub_total', function ($grn) {
                $amount = $grn->purchaseOrder?->sub_total ?? 0;
                return '
                    <div class="flex items-center justify-start gap-1">
                        <span class="iconify text-slate-400 dark:text-slate-500" data-icon="lucide:receipt" style="font-size:14px"></span>
                        <span class="font-medium text-slate-700 dark:text-slate-200">৳ ' . number_format($amount, 2) . '</span>
                    </div>
                ';
            })
            ->editColumn('discount_amount', function ($grn) {
                $amount = $grn->purchaseOrder?->discount_amount ?? 0;
                return '
                    <div class="flex items-center justify-start gap-1">
                        <span class="inline-flex items-center gap-1 px-1 py-1 rounded-full text-xs font-semibold
                            bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                            <span class="iconify" data-icon="lucide:tag" style="font-size:12px"></span>
                            − ৳ ' . number_format($amount, 2) . '
                        </span>
                    </div>
                ';
            })

            ->editColumn('tax_amount', function ($grn) {
                $amount = $grn->purchaseOrder?->tax_amount ?? 0;
                return '
                    <div class="flex items-center justify-start gap-1">
                        <span class="inline-flex items-center gap-1 px-1 py-1 rounded-full text-xs font-semibold
                            bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                            <span class="iconify" data-icon="lucide:percent" style="font-size:12px"></span>
                            ৳ ' . number_format($amount, 2) . '
                        </span>
                    </div>
                ';
            })
            ->editColumn('total_amount', function ($grn) {
                $amount = $grn->purchaseOrder?->total_amount ?? 0;
                return '
                    <div class="flex items-center justify-start gap-1">
                        <span class="inline-flex items-center gap-1.5 px-1 py-1.5 rounded-lg text-sm font-bold
                            bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200
                            dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                            <span class="iconify" data-icon="lucide:banknote" style="font-size:14px"></span>
                            ৳ ' . number_format($amount, 2) . '
                        </span>
                    </div>
                ';
            })

            ->addColumn('status_badge', function ($grn) {
                $colors = [
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'completed' => 'bg-green-100 text-green-700',
                ];
                $class = $colors[$grn->status] ?? 'bg-gray-100 text-gray-600';
                $label = ucwords(str_replace('_', ' ', $grn->status));

                return '<span class="px-2.5 py-1 rounded-full text-xs font-medium ' . $class . '">' . $label . '</span>';
            })

            ->addColumn('action', function ($grn) {
                return '
                <div class="flex items-center justify-center gap-1">
                    <a href="' . route('admin.grn.show', $grn->id) . '"
                       title="View"
                       class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                        <span class="iconify" data-icon="lucide:eye"></span>
                    </a>
                    <a href="' . route('admin.grn.edit', $grn->id) . '"
                       title="Edit"
                       class="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 hover:text-blue-700">
                        <span class="iconify" data-icon="lucide:edit"></span>
                    </a>
                    <button title="Delete"
                            class="js-delete-grn p-1.5 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700"
                            data-id="' . $grn->id . '">
                        <span class="iconify" data-icon="lucide:trash-2"></span>
                    </button>
                </div>
            ';
            })

            ->rawColumns(['grn_number','status_badge', 'action', 'sub_total','discount_amount','tax_amount','total_amount'])
            ->make(true);
    }
}
