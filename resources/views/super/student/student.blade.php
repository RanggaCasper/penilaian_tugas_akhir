@extends('layouts.app')

@section('title', 'Kelola Mahasiswa')

@section('content')
    <x-card title="Data Mahasiswa">
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
                    <th>NIM</th>
                    <th>Program Studi</th>
                    <th>Angkatan</th>
                </tr>
            </thead>
        </table>
    </x-card>

    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Ambil Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('super.student.getData') }}" method="POST">
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
        ajax: '{{ route('super.student.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'identity', name: 'identity' },
            { data: 'program_study.name', name: 'program_study.name' },
            { data: 'generation', name: 'generation' },
        ],
    });
</script>
@endpush