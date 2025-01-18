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
        ajax: '{{ route('admin.result.score.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'name', name: 'name' },
            { data: 'generation', name: 'generation' },
            { data: 'type', name: 'type' },
            { data: 'action', name: 'action' },
        ],
    });
</script>
@endpush