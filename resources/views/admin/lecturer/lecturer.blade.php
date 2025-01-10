@extends('layouts.app')

@section('title', 'Periode Ujian Tugas Akhir')

@section('content')
    <x-card title="Data Dosen">         
        <table id="datatables" class="table align-middle nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Ponsel</th>
                    <th>NIDN</th>
                    <th>NIP</th>
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
        ajax: '{{ route('admin.lecturer.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'identity', name: 'identity' },
            { data: 'secondary_identity', name: 'secondary_identity' },
        ],
    });
</script>
@endpush