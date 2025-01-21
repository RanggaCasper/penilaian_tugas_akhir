@extends('layouts.app')

@section('title', 'Kelola Dosen')

@section('content')
    <x-card title="Data Dosen">
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
            Ambil Data
        </button>  
        <table id="datatables" class="table align-middle nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Ponsel</th>
                    <th>Program Studi</th>
                    <th>NIDN</th>
                    <th>NIP</th>
                </tr>
            </thead>
        </table>
    </x-card>

    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="editPeriodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPeriodeModalLabel">Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="form_update">
                        @csrf
                        @method("PUT")
                        <div class="mb-3">
                            <x-input-field label="Nama" type="text" name="name" id="name_update" />
                        </div>
                        <div class="mb-3">
                            <x-input-field label="Email" type="email" name="email" id="email_update" />
                        </div>
                        <div class="mb-3">
                            <x-input-field label="No. Ponsel" type="text" name="phone" id="phone_update" />
                        </div>
                        <div class="mb-3">
                            <x-input-field label="NIDN" type="text" name="identity" id="identity_update" />
                        </div>
                        <div class="mb-3">
                            <x-input-field label="NIP" type="text" name="secondary_identity" id="secondary_identity_update" />
                        </div>
                        <x-button type="submit" class="btn btn-primary" label="Submit" />
                        <x-button type="reset" class="btn btn-danger" label="Reset" />
                    </form>
                </div>
            </div>
        </div>
    </div>    

    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Ambil Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('super.lecturer.getData') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="program_study_id" class="form-label">Program Studi</label>
                            <select name="program_study_id" class="form-select" id="program_study_id">
                                <option selected disabled>-- Pilih Program Studi --</option>
                                @foreach (\App\Models\ProgramStudy::all() as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-button type="submit" class="btn btn-primary" label="Submit" />
                        <x-button type="reset" class="btn btn-danger" label="Reset" />
                    </form>
                </div>
            </div>
        </div>
    </div>    
@endsection

@push('scripts')
<script>
    $('#datatables').DataTable({
        processing: true,
        serverSide: false,
        scrollX: true,
        ajax: '{{ route('super.lecturer.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'program_study.name', name: 'program_study.name' },
            { data: 'identity', name: 'identity' },
            { data: 'secondary_identity', name: 'secondary_identity' },
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
            url: '{{ route('super.lecturer.getData') }}',
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