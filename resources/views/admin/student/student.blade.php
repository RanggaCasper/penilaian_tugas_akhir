@extends('layouts.app')

@section('title', 'Kelola Mahasiswa')

@section('content')
    <x-card title="Data Mahasiswa">
        <button id="get-data" class="btn btn-primary">
            <span class="button-spinner spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
            <span class="button-text">Ambil Data</span>
        </button>        
        <table id="datatables" class="table align-middle nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Ponsel</th>
                    <th>NIM</th>
                    <th>Angkatan</th>
                </tr>
            </thead>
        </table>
    </x-card>
@endsection

@push('scripts')
<script>
    $('#datatables').DataTable({
        processing: true,
        serverSide: false,
        scrollX: true,
        ajax: '{{ route('admin.student.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'nim', name: 'nim' },
            { data: 'generation', name: 'generation' },
        ],
    });

    $('#get-data').on('click', function () {
        var $button = $(this);
        var originalText = $button.find('.button-text').text();
        var $spinner = $button.find('.button-spinner');
       
        $button.prop('disabled', true);
        $spinner.show();
        $button.find('.button-text').text('Loading...');

        $.ajax({
            url: '{{ route('admin.student.getData') }}',
            type: 'GET',
            success: function (data) {
                Swal.fire({
                    html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px"></lord-icon><div class="pt-2 mt-4 fs-15"><h4>Well done !</h4><p class="mx-4 mb-0 text-muted">' + data.message + '</p></div></div>',
                    showCancelButton: !0,
                    showConfirmButton: !1,
                    customClass: {
                        cancelButton: "btn btn-primary w-xs mb-1"
                    },
                    cancelButtonText: "Back",
                    buttonsStyling: !1,
                    showCloseButton: !0
                });
                $('#datatables').DataTable().ajax.reload();
            },
            error: function (error) {
                Swal.fire({
                    html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:120px;height:120px"></lord-icon><div class="pt-2 mt-4 fs-15"><h4>Terjadi Kesalahan !</h4><p class="mx-4 mb-0 text-muted">' + error.responseJSON.message + '</p></div></div>',
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
            complete: function () {
                $button.prop('disabled', false);
                $spinner.hide();
                $button.find('.button-text').text(originalText);
            }
        });
    });

</script>
@endpush