import $ from 'jquery';
window.$ = window.jQuery = $;

import 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';

$(function () {
    // const dataUrl = $('#po-table').data('url') ?? '/purchase-orders/data';

    $('#po-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/purchase-orders/data',
            type: 'GET',
            data: function (d) {
                d.supplier_id = $('#filter-supplier').val();
                d.status = $('#filter-status').val();
                d.order_date = $('#filter-order-date').val();
            },
            dataSrc: function (json) {
                console.log("DataTables response:", json);
                return json.data;
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
            }
        },
        columnDefs: [
            {
                targets: '_all',
                createdCell: function (td) {
                    $(td).addClass('px-4 py-3');
                }
            },
            {
                targets: 4, // Items column
                createdCell: function (td) {
                    $(td).addClass('text-center');
                }
            },
            {
                targets: 5, // Amount column
                createdCell: function (td) {
                    $(td).addClass('text-right');
                }
            },
            {
                targets: -1, // Action column
                createdCell: function (td) {
                    $(td).addClass('text-center');
                }
            }
        ],
        columns: [
            { data: 'po_number', name: 'po_number' },
            { data: 'supplier', name: 'supplier', searchable: true },
            { data: 'order_date', name: 'order_date' },
            { data: 'expected_delivery_date', name: 'expected_delivery_date' },
            { data: 'items_count', name: 'items_count', orderable: false, searchable: false },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: true },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']],
        rowCallback: function (row) {
            $(row).addClass('hover:bg-gray-50 dark:hover:bg-white/5 transition-colors divide-y divide-gray-100');
        },
        language: {
            search: "",
            searchPlaceholder: "Search purchase orders..."
        }
    });

// Reload table on filter change
    $('#filter-supplier, #filter-status, #filter-order-date').on('change', function () {
        $('#po-table').DataTable().ajax.reload();
    });

    $('#filter-reset').on('click', function () {
        $('#filter-supplier').val('');
        $('#filter-status').val('');
        $('#filter-order-date').val('');
        $('#po-table').DataTable().ajax.reload();
    });

// Delete handler

    // Reload table on filter change
    $('#filter-supplier, #filter-status').on('change', function () {
        table.ajax.reload();
    });

    $('#filter-order-date').on('change', function () {
        table.ajax.reload();
    });

    $('#filter-reset').on('click', function () {
        $('#filter-supplier').val('');
        $('#filter-status').val('');
        $('#filter-order-date').val('');
        table.ajax.reload();
    });

});
