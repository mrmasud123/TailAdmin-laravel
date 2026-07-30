import $ from 'jquery';
window.$ = window.jQuery = $;

import 'select2/dist/css/select2.css';
import Select2 from 'select2/dist/js/select2.full.js';
Select2(window, $);

$(function () {
    const $form = $('#purchase-order-form');
    const $tbody = $('#po-items-body');

    // Continue new row indices after the highest existing data-row index,
    // so newly added rows never collide with existing item indices.
    let rowIndex = 0;
    $tbody.find('tr[data-row]').each(function () {
        const idx = parseInt($(this).data('row'), 10);
        if (!isNaN(idx) && idx >= rowIndex) {
            rowIndex = idx + 1;
        }
    });

    function rowTemplate(index) {
        return `
            <tr data-row="${index}">
                <td class="px-4 py-2.5">
                    <select class="js-product-select w-full" name="items[${index}][product_id]" required></select>
                </td>
                <td class="px-4 py-2.5">
                    <input type="number" min="1" step="1" value="1" class="js-qty w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-center" name="items[${index}][qty]" required>
                </td>
                <td class="px-4 py-2.5">
                    <input type="number" min="0" step="0.01" value="0" class="js-cost w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-right" name="items[${index}][cost]" required>
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

    // For a NEW row: full ajax-driven Select2, no preselected option yet.
    function initNewProductSelect2($select) {
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

    // For an EXISTING row: the correct <option> is already rendered by Blade
    // and marked selected, so Select2 just needs to attach to it — no ajax
    // call needed until the user actually searches for something new.
    function initExistingProductSelect2($select) {
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
        initNewProductSelect2($row.find('.js-product-select'));
        recalcTotals();
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
        $tbody.find('tr[data-row]').each(function () {
            const $row = $(this);
            const qty = parseFloat($row.find('.js-qty').val()) || 0;
            const cost = parseFloat($row.find('.js-cost').val()) || 0;
            const lineTotal = qty * cost;
            $row.find('.js-line-total').text(lineTotal.toLocaleString());
            subtotal += lineTotal;
        });

        const discount = parseFloat($('.js-discount').val()) || 0;
        const tax = parseFloat($('.js-tax').val()) || 0;
        const grandTotal = subtotal - discount + tax;

        $('#po-subtotal').text(subtotal.toLocaleString());
        $('#po-grand-total').text(grandTotal.toLocaleString());
    }

    function clearErrors() {
        $('.js-error').text('');
    }

    function showErrors(errors) {
        clearErrors();
        $.each(errors, function (field, messages) {
            const baseField = field.split('.')[0];
            $(`.js-error[data-field="${baseField}"]`).text(messages[0]);
        });
    }

    $tbody.find('tr[data-row] .js-product-select').each(function () {
        initExistingProductSelect2($(this));
    });

    $('#add-row-btn').on('click', addRow);

    $tbody.on('click', '.js-remove-row', function () {
        removeRow($(this).closest('tr'));
    });

    $tbody.on('input', '.js-qty, .js-cost', recalcTotals);
    $(document).on('input', '.js-discount, .js-tax', recalcTotals);

    $form.on('click', '.js-submit-btn', function () {
        $('#action').val($(this).val());
    });


    $form.on('submit', function (e){
        e.preventDefault();
        clearErrors();

        if ($tbody.find('tr[data-row]').length === 0) {
            $(`.js-error[data-field="items"]`).text('At least one item is required.');
            return;
        }
        const $submitButtons = $form.find('.js-submit-btn').prop('disabled', true);

        Swal.fire({
            title: 'Saving...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function (response) {
                Swal.fire({
                    title: 'Success!',
                    text: response.message ?? 'Purchase order updated.',
                    icon: 'success'
                }).then(() => {
                    window.location.href = '/purchase-orders';
                });
            },
            error: function (xhr) {
                Swal.close();

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    console.log(xhr);
                    showErrors(xhr.responseJSON.errors);
                    Swal.fire({
                        title: 'Validation error',
                        text: 'Please check the highlighted fields.',
                        icon: 'error'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong while updating the purchase order.',
                        icon: 'error'
                    });
                }
            },
            complete: function () {
                $submitButtons.prop('disabled', false);
            }
        });
    });

    recalcTotals();
});
