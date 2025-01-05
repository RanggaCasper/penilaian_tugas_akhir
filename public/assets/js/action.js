$(document).on('submit', 'form', function(e) {
    e.preventDefault();

    $(this).find('.form-control').removeClass('is-invalid');
    $('.alert').remove();
    $('.error-message').remove();

    let button = $(this).find('button[type="submit"]');
    let buttonText = button.text();
    button.prop('disabled', true);
    button.html(`
        <span class="button-spinner spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...
        <span class="button-text" style="display: none;">${buttonText}</span>
    `);

    $.ajax({
        url: $(this).attr('action'),
        method: $(this).attr('method') || 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.status) {
                $(e.target).before(`
                    <div class="mb-3 text-white alert alert-success alert-dismissible bg-success alert-label-icon fade show material-shadow" role="alert">
                        <i class="ri-check-line label-icon"></i><strong>Berhasil</strong> - ${response.message}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);

                const modal = $(e.target).closest('.modal');
                if (modal.length > 0) {
                    modal.modal('hide');
                }
            }

            if (response.redirect_url) {
                window.location.href = response.redirect_url;
            }

            if ($.fn.DataTable) {
                $('.dataTable').each(function () {
                    if ($.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable().ajax.reload(null, false);
                    }
                });
            }

            
            if (!$(e.target).attr('id')?.endsWith('_update')) {
                if ($(e.target).data('reset') !== false) {
                    $(e.target).trigger('reset');
                }

                if ($.fn.select2) {
                    $('.select2').each(function() {
                        $(this).val(null).trigger('change');
                    });
                }
            }

            $.each($(e.target).data(), function(key, value) {
                if (typeof window[value] === 'function') {
                    try {
                        window[value]();
                    } catch (err) {
                        console.error(`Error saat memanggil fungsi dari data-${key}:`, err);
                    }
                }
            });
        },
        error: function(xhr) {
            let response = xhr.responseJSON;
            let errors = response.errors;

            if (errors && Object.keys(errors).length > 0) {
                $.each(errors, function(field, message) {
                    let inputName = field.replace(/\.(\d+)/g, '[$1]');
                    inputName = inputName.replace(/\./g, '\\.').replace(/\[/g, '\\[').replace(/\]/g, '\\]');
                    let input = $(`[name="${inputName}"]`);

                    input.addClass('is-invalid');

                    input.after(`
                        <div class="error-message text-danger mt-1">${message[0]}</div>
                    `);
                });
            } else if (response.message) {
                $(e.target).before(`
                    <div class="mb-3 text-white alert alert-danger alert-dismissible bg-danger alert-label-icon fade show material-shadow" role="alert">
                        <i class="ri-error-warning-line label-icon"></i><strong>Danger</strong> - ${response.message}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
            }
        },
        complete: function() {  
            button.prop('disabled', false);
            button.html(`
                <span class="button-text">${buttonText}</span>
                <span class="button-spinner spinner-border spinner-border-sm" style="display: none;" role="status" aria-hidden="true"></span>
            `);
        }
    });
});
