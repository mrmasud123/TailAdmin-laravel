<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderRequest;
use App\Models\GoodsReceiptNote;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\PurchaseOrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Format;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly PurchaseOrderService $purchaseOrderService
    ) {}


    public function index() {
        $suppliers= Supplier::all();
        return view('admin.purchase.index', compact('suppliers'));
    }

    public function dashboard()
    {
        $stats = $this->buildStats();
        $recentOrders = $this->buildRecentOrders();

        return view('admin.purchase.dashboard', compact('stats', 'recentOrders'));
    }

    private function buildStats(): array
    {
        $openPoCount = PurchaseOrder::whereIn('status', ['draft', 'sent', 'partially_received'])->count();
        $pendingApprovalCount = PurchaseOrder::where('status', 'draft')->count();

        $monthStart = Carbon::now()->startOfMonth();
        $grnsThisMonth = GoodsReceiptNote::whereBetween('received_date', [$monthStart, Carbon::now()])
            ->with('purchaseOrder:id,total_amount')
            ->get();
        $grnAmountThisMonth = $grnsThisMonth->sum(fn ($grn) => $grn->purchaseOrder?->total_amount ?? 0);

        // Outstanding payables = invoice totals minus what's been paid so far.
        $invoices = PurchaseInvoice::withSum('payments', 'amount')->get();
        $outstandingPayables = $invoices->sum(fn ($inv) => max($inv->total_amount - ($inv->payments_sum_amount ?? 0), 0));
        $dueInvoiceCount = $invoices->filter(fn ($inv) => ($inv->total_amount - ($inv->payments_sum_amount ?? 0)) > 0)->count();

        $returnsThisMonth = PurchaseReturn::whereMonth('return_date', now()->month)
            ->whereYear('return_date', now()->year)
            ->get();

        return [
            [
                'label' => 'Open Purchase Orders',
                'value' => (string) $openPoCount,
                'hint' => "{$pendingApprovalCount} awaiting approval",
                'icon' => 'doc',
                'color' => 'blue',
            ],
            [
                'label' => 'Goods Received (This Month)',
                'value' => '৳ ' . number_format($grnAmountThisMonth, 0),
                'hint' => $grnsThisMonth->count() . ' GRNs posted',
                'icon' => 'inbox',
                'color' => 'emerald',
            ],
            [
                'label' => 'Outstanding Payables',
                'value' => '৳ ' . number_format($outstandingPayables, 0),
                'hint' => "{$dueInvoiceCount} invoices due",
                'icon' => 'wallet',
                'color' => 'amber',
            ],
            [
                'label' => 'Purchase Returns',
                'value' => (string) $returnsThisMonth->count(),
                'hint' => '৳ ' . number_format($returnsThisMonth->sum('total_amount'), 0) . ' total value',
                'icon' => 'reply',
                'color' => 'red',
            ],
        ];
    }

    private function buildRecentOrders()
    {
        return PurchaseOrder::with('supplier:id,name')
            ->select(['id', 'po_number', 'supplier_id', 'order_date', 'total_amount', 'status'])
            ->latest('order_date')
            ->take(5)
            ->get();
    }


    public function create() {
        $suppliers=Supplier::all();
        return view('admin.purchase.create', compact('suppliers'));
    }

        public function store(StorePurchaseOrderRequest $request)
        {
            $purchaseOrder = $this->purchaseOrderService->create(
                $request->validated(),
                $request->user()
            );
            return ApiResponseHelper::success(
                'Purchase order saved successfully.',
                $purchaseOrder,
                201);

        }
    public function edit(PurchaseOrder $purchaseOrder) {
        $suppliers= Supplier::all();
        $purchaseOrder->load([
            'supplier',
            'items.product',
            'items.product:id,name'
        ]);
//        return $purchaseOrder;
        return view('admin.purchase.edit', compact('suppliers', 'purchaseOrder'));
    }

    public function update(UpdatePurchaseOrderRequest $request) {
        $purchaseOrder = $this->purchaseOrderService->update(
            $request->validated(),
            $request->user()
        );
        return ApiResponseHelper::success(
            'Purchase order saved successfully.',
            $purchaseOrder,
            201);
    }

    public function destroy(PurchaseOrder $purchaseOrder) {}

    public function print(PurchaseOrder $purchaseOrder) {
        $purchaseOrder->load(['supplier', 'items.product', 'createdBy']);
        $footer = '
            <div style="width:100%; padding:0 20px; font-size:10px;">
                <div style="display:flex; justify-content:space-between;">
                    <div style="width:160px; text-align:center;">
                        <div style="border-top:1px solid #666; padding-top:4px;">
                            Prepared By
                        </div>
                    </div>

                    <div style="width:160px; text-align:center;">
                        <div style="border-top:1px solid #666; padding-top:4px;">
                            Approved By
                        </div>
                    </div>
                </div>
            </div>';


        return Pdf::view('admin.purchase.print', [
            'po' => $purchaseOrder,
        ])
            ->withBrowsershot(function ($browsershot) use ($footer) {
                $browsershot
                    ->showBrowserHeaderAndFooter()
                    ->footerHtml($footer)
                    ->headerHtml('<div></div>');
            })
            ->format(Format::A5)
            ->margins(10, 10, 30, 10)
            ->name("purchase-order-{$purchaseOrder->po_number}.pdf");
    }

    public function pendingApproval() {
        return view('admin.purchase.pending-approval');
    }

    public function approve(PurchaseOrder $purchaseOrder) {}

    public function reject(PurchaseOrder $purchaseOrder) {}

    public function bySupplierReport() {}

    public function data(Request $request)
    {
        return DataTables::of(
            PurchaseOrder::query()
                ->select([
                    'id',
                    'po_number',
                    'supplier_id',
                    'order_date',
                    'expected_delivery_date',
                    'total_amount',
                    'status',
                ])
                ->when($request->filled('status'), function($query)use($request){
                    $query->where('status', $request->status);
                })
                ->when($request->filled('supplier_id'), function($query)use($request){
                    $query->where('supplier_id', $request->supplier_id);
                })
                ->with(['supplier'])
                ->withCount('items')
        )

            ->addColumn('items_count', function ($po) {
                return $po->items_count;
            })

            ->addColumn('supplier', function ($po) {
                return $po->supplier?->name ?? 'N/A';
            })

            ->editColumn('order_date', function ($po) {
                return Carbon::parse($po->order_date)->format('Y-m-d');
            })

            ->editColumn('expected_delivery_date', function ($po) {
                return Carbon::parse($po->expected_delivery_date)->format("Y-m-d");
            })

            ->editColumn('total_amount', function ($po) {
                return '৳ ' . number_format($po->total_amount, 2);
            })

            ->addColumn('status_badge', function ($po) {
                $colors = [
                    'draft' => 'bg-gray-100 text-gray-600',
                    'sent' => 'bg-blue-100 text-blue-600',
                    'partially_received' => 'bg-yellow-100 text-yellow-700',
                    'received' => 'bg-green-100 text-green-700',
                    'closed' => 'bg-gray-200 text-gray-700',
                    'cancelled' => 'bg-red-100 text-red-600',
                ];
                $class = $colors[$po->status] ?? 'bg-gray-100 text-gray-600';
                $label = ucwords(str_replace('_', ' ', $po->status));

                return '<span class="px-2.5 py-1 rounded-full text-xs font-medium ' . $class . '">' . $label . '</span>';
            })

            ->addColumn('action', function ($po) {
                return '
                <div class="flex items-center justify-center gap-1">
                    <a href="' . route('admin.purchase-order.print', $po->id) . '"
                       title="Print"
                       class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                        <span class="iconify" data-icon="lucide:printer"></span>
                    </a>
                    <a href="' . route('admin.purchase-order.edit', $po->id) . '"
                       title="Edit"
                       class="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 hover:text-blue-700">
                        <span class="iconify" data-icon="lucide:edit"></span>
                    </a>
                    <button title="Delete"
                            class="js-delete-po p-1.5 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700"
                            data-id="' . $po->id . '">
                        <span class="iconify" data-icon="lucide:trash-2"></span>
                    </button>
                </div>
            ';
            })

            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}
