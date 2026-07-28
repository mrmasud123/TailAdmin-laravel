import $ from 'jquery';
window.$ = window.jQuery = $;

import 'select2/dist/css/select2.css';
import Select2 from 'select2/dist/js/select2.full.js';
Select2(window, $);

$(function () {
    const $tbody = $('#po-items-body');
    let rowIndex = 0;

    function rowTemplate(index) {
        return `
            <tr data-row="${index}">
                <td class="px-4 py-2.5">
                    <select class="js-product-select w-full" name="items[${index}][product_id]"></select>
                </td>
                <td class="px-4 py-2.5">
                    <input type="number" min="1" value="1" class="js-qty w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-center" name="items[${index}][qty]">
                </td>
                <td class="px-4 py-2.5">
                    <input type="number" min="0" step="0.01" value="0" class="js-cost w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-right" name="items[${index}][cost]">
                </td>
                <td class="px-4 py-2.5 text-right font-medium text-gray-700 dark:text-gray-200 js-line-total">0</td>
                <td class="px-4 py-2.5 text-center">
                    <button type="button" class="js-remove-row text-red-500 hover:text-red-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </td>
            </tr>
        `;
    }

    function initProductSelect2($select) {
        // Destroy first in case anything else touched this element — guarantees a clean single instance
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2({
            width: '100%',
            placeholder: 'Select product...',
            minimumInputLength: 1,
            ajax: {
                url: '/get-products',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: function (data) {
                    return {
                        results: (data || []).map(function (p) {
                            return { id: p.id, text: p.name };
                        })
                    };
                },
                cache: true
            }
        });
    }

    function addRow() {
        const index = rowIndex++;
        const $row = $(rowTemplate(index));
        $tbody.append($row);
        initProductSelect2($row.find('.js-product-select'));
    }

    function removeRow($row) {
        if ($tbody.find('tr').length <= 1) return;
        const $select = $row.find('.js-product-select');
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }
        $row.remove();
        recalcTotals();
    }

    function recalcTotals() {
        let subtotal = 0;
        $tbody.find('tr').each(function () {
            const $row = $(this);
            const qty = parseFloat($row.find('.js-qty').val()) || 0;
            const cost = parseFloat($row.find('.js-cost').val()) || 0;
            const lineTotal = qty * cost;
            $row.find('.js-line-total').text(lineTotal.toLocaleString());
            subtotal += lineTotal;
        });
        $('#po-subtotal').text(subtotal.toLocaleString());
        $('#po-grand-total').text(subtotal.toLocaleString());
    }

    $('#add-row-btn').on('click', addRow);

    $tbody.on('click', '.js-remove-row', function () {
        removeRow($(this).closest('tr'));
    });

    $tbody.on('input', '.js-qty, .js-cost', recalcTotals);

    addRow();
    addRow();
    recalcTotals();
});
