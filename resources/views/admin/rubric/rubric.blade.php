@extends('layouts.app')

@section('title', 'Penilaian')

@section('content')
<x-card title="Tambah Penilaian">
    <form action="{{ route('admin.rubric.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <x-input-field label="Nama" type="text" name="name" id="nama" />
        </div>
        <div class="mb-3">
            <label for="type">Tipe</label>
            <select name="type" class="form-control form-select" id="type">
                <option selected disabled>-- Pilih Tipe --</option>
                <option value="proposal">Proposal</option>
                <option value="thesis">Tugas Akhir</option>
                <option value="guidance">Bimbingan</option>
            </select>
        </div>
        <x-button type="submit" class="btn btn-primary" label="Submit" />
        <x-button type="reset" class="btn btn-danger" label="Reset" />
    </form>
</x-card>
<x-card title="Data Penilaian">   
    <table id="datatables" class="table align-middle nowrap">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tipe</th>
                <th>Aksi</th>
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
                        <label for="type_update">Tipe</label>
                        <select name="type" class="form-control form-select" id="type_update">
                            <option selected disabled>-- Pilih Tipe --</option>
                            <option value="proposal">Proposal</option>
                            <option value="thesis">Tugas Akhir</option>
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
            ajax: '{{ route('admin.rubric.get') }}',
            columns: [
                { data: 'no', name: 'no' },
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'action', name: 'action' },
            ],
        });

        $('#datatables').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: '{{ route("admin.rubric.getById", ["id" => ":id"]) }}'.replace(':id', id),
                type: 'GET',
                success: function(data) {
                    $('#form_update').attr('action', '{{ route("admin.rubric.update", ["id" => ":id"]) }}'.replace(':id', id));
                    $('#name_update').val(data.name);
                    $('#type_update').val(data.type);
                },
                error: function(error) {
                    console.error(error);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat mengambil data kategori.',
                        'error'
                    );
                }
            });
        });

        $('#datatables').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="pt-2 mx-5 mt-4 fs-15"><h4>Apakah anda yakin?</h4><p class="mx-4 mb-0 text-muted">Anda tidak akan dapat mengembalikan data ini!</p></div></div>',
                showCancelButton: !0,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mb-1",
                    cancelButton: "btn btn-danger w-xs mb-1"
                },
                confirmButtonText: "Ya, Hapus!",
                buttonsStyling: !1,
                showCloseButton: !0
            }).then(function(t) {
                if(t.value) {
                    $.ajax({
                        url: '{{ route("admin.rubric.destroy", ["id" => ":id"]) }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(data) {
                            Swal.fire({
                                html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px"></lord-icon><div class="pt-2 mt-4 fs-15"><h4>Well done !</h4><p class="mx-4 mb-0 text-muted">' +data.message+ '</p></div></div>',
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
                        error: function(error) {
                            console.error(error);
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
                }
            })
        });
    </script>
@endpush