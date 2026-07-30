import $ from 'jquery';
window.$ = window.jQuery = $;
import 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';

$(function () {
    const table = $('#grn-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/goods-receipts/data",
            data: function (d) {
                d.supplier_id = $('#filter_supplier').val();
                d.received_date = $('#filter_receipt_date').val();
            }
        },
        columns: [
            { data: 'grn_number', name: 'grn_number' },
            // { data: 'po_number', name: 'purchaseOrder.po_number', orderable: false, searchable: false },
            { data: 'supplier', name: 'supplier.name', orderable: false, searchable: false },
            { data: 'received_date', name: 'received_date',orderable: false },
            { data: 'sub_total', name: 'purchaseOrder.sub_total',orderable: false },
            { data: 'discount_amount', name: 'purchaseOrder.discount_amount',orderable: false },
            { data: 'tax_amount', name: 'purchaseOrder.taxt_amount',orderable: false },
            { data: 'total_amount', name: 'purchaseOrder.total_amount',orderable: false },
            // { data: 'items_count', name: 'items_count', orderable: false, searchable: false, className: 'text-center' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ]
    });

    $('#btn_filter').on('click', () => table.draw());
    $('#btn_reset').on('click', function () {
        $('#filter_supplier').val('').trigger('change');
        $('#filter_receipt_date').val('');
        table.draw();
    });
});
/*sub_total	"1335.00"
discount_amount	"50.00"
tax_amount	"100.00"
total_amount	"1385.00"*/
