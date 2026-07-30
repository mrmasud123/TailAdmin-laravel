import $ from 'jquery';
window.$ = window.jQuery = $;
$(function () {

    // Initialize each item group
    $('.js-item-group').each(function () {
        initItemGroup($(this));
    });

    function initItemGroup($group) {
        const $addBtn    = $group.find('.js-add-batch');
        const $batchBody = $group.find('.js-batch-rows');

        $addBtn.on('click', function () {
            addBatchRow($group);
        });

        $batchBody.on('click', '.js-remove-batch', function () {
            $(this).closest('.js-batch-row').remove();
            reindexRows($group);
            validateAllocation($group);
        });

        $batchBody.on('input', 'input[name*="[quantity]"]', function () {
            validateAllocation($group);
        });

        validateAllocation($group);
    }

    function addBatchRow($group) {
        const $batchBody = $group.find('.js-batch-rows');
        const $rows       = $batchBody.find('.js-batch-row');
        const $firstRow   = $rows.first();
        const newIndex    = $rows.length;

        const $clone = $firstRow.clone();

        $clone.find('input').each(function () {
            $(this).val('');
            const name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace(/\[batches\]\[\d+\]/, `[batches][${newIndex}]`));
            }
        });

        $clone.find('[id]').each(function () {
            const id = $(this).attr('id');
            $(this).attr('id', id.replace(/_\d+$/, `_${newIndex}`));
        });

        $clone.find('.js-remove-batch').removeClass('hidden');

        $batchBody.append($clone);
        // initDatePickers($clone);

        reindexRows($group);
        validateAllocation($group);
    }

    function reindexRows($group) {
        const $rows = $group.find('.js-batch-row');

        $rows.each(function (i) {
            $(this).find('input').each(function () {
                const name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\[batches\]\[\d+\]/, `[batches][${i}]`));
                }
            });
            $(this).find('[id]').each(function () {
                const id = $(this).attr('id');
                $(this).attr('id', id.replace(/_\d+$/, `_${i}`));
            });
        });

        $rows.find('.js-remove-batch').toggleClass('hidden', $rows.length <= 1);
    }

    function validateAllocation($group) {
        const pendingQty = parseFloat($group.data('pending'));
        let total = 0;

        $group.find('input[name*="[quantity]"]').each(function () {
            total += parseFloat($(this).val()) || 0;
        });

        const remaining = pendingQty - total;
        let $notice = $group.find('.js-allocation-notice');

        if ($notice.length === 0) {
            $notice = $('<p class="js-allocation-notice text-xs mt-1"></p>');
            $group.find('.js-batch-table').after($notice);
        }

        if (remaining === 0) {
            $notice.text(`Fully allocated (${total} of ${pendingQty}).`)
                .attr('class', 'js-allocation-notice text-xs mt-1 text-green-600');
            $group.data('valid', true);
        } else if (remaining > 0) {
            $notice.text(`${remaining} unit(s) not yet allocated to a batch (${total} of ${pendingQty}).`)
                .attr('class', 'js-allocation-notice text-xs mt-1 text-amber-600');
            $group.data('valid', false);
        } else {
            $notice.text(`Over-allocated by ${Math.abs(remaining)} unit(s) — exceeds pending quantity (${total} of ${pendingQty}).`)
                .attr('class', 'js-allocation-notice text-xs mt-1 text-red-600');
            $group.data('valid', false);
        }
    }

    function initDatePickers($scope) {

    }

    $('#grn-form').on('submit', function (e) {
        let hasError = false;

        $('.js-item-group').each(function () {
            const $group = $(this);
            validateAllocation($group);

            if ($group.data('valid') === false) {
                hasError = true;
                $group.find('.js-batch-table').addClass('ring-1 ring-red-400 rounded-lg');
            } else {
                $group.find('.js-batch-table').removeClass('ring-1 ring-red-400 rounded-lg');
            }
        });

        if (hasError) {
            e.preventDefault();
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Batch quantities don\'t match',
                    text: 'Every item\'s batch quantities must add up exactly to its pending quantity before you can confirm receipt.',
                });
            } else {
                alert('Batch quantities don\'t match the pending quantity for one or more items.');
            }
        }



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

});
