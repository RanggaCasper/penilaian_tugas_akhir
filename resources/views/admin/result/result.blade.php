@extends('layouts.app')

@section('content')
<x-card title="Hasil Ujian">   
    <table id="datatables" class="table align-middle nowrap">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Angkatan</th>
                <th>Tipe</th>
                <th>Aksi</th>
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
        ajax: '{{ route('admin.result.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'name', name: 'name' },
            { data: 'generation', name: 'generation' },
            { data: 'type', name: 'type' },
            { data: 'action', name: 'action' },
        ],
    });

    $('#datatables').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '{{ route("admin.result.getById", ["id" => ":id"]) }}'.replace(':id', id),
            type: 'GET',
            success: function(data) {
                $('#form_update').attr('action', '{{ route("admin.result.update", ["id" => ":id"]) }}'.replace(':id', id));
                $('#judul_update').val(data.name);
                $('#start_date_update').val(data.start_date);
                $('#end_date_update').val(data.end_date);
                $('#generation_update').val(data.generation_id);
                $('#type_update').val(data.type);
                $('#is_active_update').prop('checked', !!data.is_active);
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
</script>
@endpush