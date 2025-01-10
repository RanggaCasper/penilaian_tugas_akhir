@extends('layouts.app')

@section('content')
    <x-card title="Pengaturan API">
        <form action="{{ route('special.api.update') }}" data-reset="false" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <x-input-field label="API ID" type="text" value="{{ $data->api_id }}" attr="disabled" />
            </div>
            <div class="mb-3">
                <label for="api_key" class="form-label">API Key</label>
                <div class="input-group">
                    <input type="text" id="api_key" class="form-control" value="{{ $data->api_key }}" disabled>
                    <button class="btn btn-primary material-shadow-none" id="regenerate" type="button">Ubah</button>
                </div>
            </div>
            <div class="mb-3">
                <x-input-field 
                    name="ips"
                    label="Whitelist IP" 
                    type="text" 
                    placeholder="Contoh: 192.168.1.1, 192.168.1.2"
                    value="{{ is_array($data->ips) && !empty($data->ips[0]) ? implode(', ', $data->ips) : '' }}" 
                />
                <small class="text-muted">Gunakan koma (,) untuk memisahkan beberapa alamat IP</small>
            </div>
            <x-button type="submit" class="btn btn-primary" label="Submit" />
            <x-button type="reset" class="btn btn-danger" label="Reset" />
        </form>
    </x-card>
@endsection

@push('scripts')
    <script>
         $('#regenerate').click(function () {
            let button = $(this);
            let buttonText = button.text();
            button.prop('disabled', true);
            button.html(`
                <span class="button-spinner spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...
                <span class="button-text" style="display: none;">${buttonText}</span>
            `);
        
            $.ajax({
                url: '{{ route('special.api.regenerate') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    $('#api_key').val(response.data.api_key);
                    Swal.fire({
                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px"></lord-icon><div class="pt-2 mt-4 fs-15"><h4>Well done !</h4><p class="mx-4 mb-0 text-muted">' +response.message+ '</p></div></div>',
                        showCancelButton: !0,
                        showConfirmButton: !1,
                        customClass: {
                            cancelButton: "btn btn-primary w-xs mb-1"
                        },
                        cancelButtonText: "Back",
                        buttonsStyling: !1,
                        showCloseButton: !0
                    });
                },
                error: function (error) {
                    Swal.fire({
                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:120px;height:120px"></lord-icon><div class="pt-2 mt-4 fs-15"><h4>Terjadi Kesalahan !</h4><p class="mx-4 mb-0 text-muted">' +error.responseJSON.message+ '</p></div></div>',
                        showCancelButton: !0,
                        showConfirmButton: !1,
                        customClass: {
                            cancelButton: "btn btn-primary w-xs mb-1"
                        },
                        cancelButtonText: "Back",
                        buttonsStyling: !1,
                        showCloseButton: !0
                    })
                },
                complete: function () {
                    button.prop('disabled', false);
                    button.html(`
                        <span class="button-text">${buttonText}</span>
                        <span class="button-spinner spinner-border spinner-border-sm" style="display: none;" role="status" aria-hidden="true"></span>
                    `);
                }
            });
        });
    </script>
@endpush