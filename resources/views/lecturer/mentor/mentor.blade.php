@extends('layouts.app')

@section('content')
<x-card title="Data Mahasiswa Bimbingan">   
    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
        Import
    </button> 
    <table id="datatables" class="table align-middle nowrap">
        <thead>
            <tr>
                <th>No</th>
                <th>Mahasiswa</th>
                <th>NIM</th>
                <th>Judul</th>
                <th>Posisi</th>
                <th>Nilai</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</x-card>

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Format Excel</h6>
                <a href="{{ route('lecturer.mentor.export.template') }}" class="mb-3 btn btn-primary btn-sm">Download</a>
                <form action="{{ route('lecturer.mentor.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <x-input-field label="File Excel" type="file" name="file" id="file" />
                        <p class="p-0 m-0 mb-3 small text-muted">*Gunakan Format Excel Untuk Mengimport Data</p>
                    </div>
                    <x-button type="submit" class="btn btn-primary" label="Submit" />
                    <x-button type="reset" class="btn btn-danger" label="Reset" />
                </form>
            </div>
        </div>
    </div>
</div>   

<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModal">Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form">
                    @csrf
                    <div class="mb-3">  
                        <x-input-field label="Nilai" type="number" name="score" id="score" />
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
        ajax: '{{ route('lecturer.mentor.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'student.name', name: 'student.name' },
            { data: 'student.identity', name: 'student.identity' },
            { data: 'title', name: 'title' },
            { data: 'position', name: 'position' },
            { data: 'score', name: 'score' },
            { data: 'action', name: 'action' },
        ],
    });

    $('#datatables').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $('#form').find('input[name="proposal_id"]').remove();
        $.ajax({
            url: '{{ route("lecturer.mentor.getScoreById", ["id" => ":id"]) }}'.replace(':id', id),
            type: 'GET',
            success: function(data) {
                $('<input type="hidden" name="proposal_id">').val(id).appendTo('#form');
                $('#score').val(data.score);
            },
            error: function(error) {
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
            }
        });
    });
</script>
@endpush