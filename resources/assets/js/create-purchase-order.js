import $ from 'jquery';
window.$ = window.jQuery = $;

import 'select2/dist/css/select2.css';
import Select2 from 'select2/dist/js/select2.full.js';
Select2(window, $);

$(function () {
    const $form = $('#purchase-order-form');
    const $tbody = $('#po-items-body');
    let rowIndex = 0;

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

    function initProductSelect2($select) {
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
            // handles both top-level fields (supplier_id) and items.0.product_id style keys
            const baseField = field.split('.')[0];
            $(`.js-error[data-field="${baseField}"]`).text(messages[0]);
        });
    }

    // ---- Row events ----
    $('#add-row-btn').on('click', addRow);

    $tbody.on('click', '.js-remove-row', function () {
        removeRow($(this).closest('tr'));
    });

    $tbody.on('input', '.js-qty, .js-cost', recalcTotals);
    $(document).on('input', '.js-discount, .js-tax', recalcTotals);

    // ---- Form submit ----
    let clickedAction = 'draft';

    $form.on('click', '.js-submit-btn', function () {
        clickedAction = $(this).val();
    });

    $('#purchase-order-form').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const actionUrl = $(form).attr('action');

        let method = $(form).find('input[name="_method"]').val() || 'POST';
        console.log("Form method:", method);
        Swal.fire({
            title: 'Processing...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: actionUrl,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(form).find('button[type="submit"]').prop('disabled', true);
            },
            complete: function () {
                $(form).find('button[type="submit"]').prop('disabled', false);
            },
            success: function (response) {
                console.log(response);
                Swal.close();

                Swal.fire({
                    title: 'Success!',
                    text: response.message,
                    icon: 'success'
                }).then(() => {
                    // window.location.href = "/products";
                });
            },

            error: function (xhr) {
                Swal.close();

                let errorMsg = 'Something went wrong';

                if (xhr.responseJSON?.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors)[0][0];
                }

                Swal.fire({
                    title: 'Error!',
                    text: errorMsg,
                    icon: 'error'
                });
            }
        });
    });

    addRow();
    addRow();
    recalcTotals();
});
