@extends('layouts.app')

@section('title', 'Kelola Mahasiswa')

@section('content')
    <x-card title="Data Mahasiswa">    
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
            { data: 'identity', name: 'identity' },
            { data: 'generation', name: 'generation' },
        ],
    });
</script>
@endpush