$(document).on('submit', 'form', function(e) {
    e.preventDefault();

    $(this).find('.message-error').text('');
    $(this).find('.form-control').removeClass('is-invalid');
    
    $('.alert').remove();

    let button = $(this).find('button[type="submit"]');
    button.prop('disabled', true);
    button.html(`
        <span class="button-spinner spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        <span class="button-text" style="display: none;">${button.text()}</span>
    `);

    $.ajax({
        url: $(this).attr('action'),
        method: $(this).attr('method') || 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if(response.status) {
                $(e.target).before(`
                    <div class="mb-3 text-white alert alert-success alert-dismissible bg-success alert-label-icon fade show material-shadow" role="alert">
                        <i class="ri-check-line label-icon"></i><strong>Success</strong> - ${response.message}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
            }
        },
        error: function(xhr) {
            let response = xhr.responseJSON;
            let errors = response.errors;

            if (errors && Object.keys(errors).length > 0) {
                $.each(errors, function(field, message) {
                    let input = $(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.closest('.mb-3').find('.message-error').text(message[0]);
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
                <span class="button-text">${button.text()}</span>
                <span class="button-spinner spinner-border spinner-border-sm" style="display: none;" role="status" aria-hidden="true"></span>
            `);
        }
    });
});
