@extends('layouts.app')

@section('content')
@if (!empty($proposal_score['scores']))
    <x-card title="Perbaikan Ujian Proposal">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Posisi Penguji</th>
                        <th>Nama Penguji</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proposal_score['scores'] as $score)
                        <tr>
                            <td>{{ $score['position'] }}</td>
                            <td>{{ $score['name'] }}</td>
                            <td><a href="{{ route('student.result.revisi.generatePdf', ['type' => 'proposal', 'id' => $score['id']]) }}" class="btn btn-primary btn-sm">Download</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data untuk proposal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@else
    <x-card title="Perbaikan Ujian Proposal">
        <p class="text-center">Data tidak ditemukan.</p>
    </x-card>
@endif


@if (!empty($thesis_score['scores']))
    <x-card title="Perbaikan Ujian Tugas Akhir">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Posisi Penguji</th>
                        <th>Nama Penguji</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($thesis_score['scores'] as $score)
                        <tr>
                            <td>{{ $score['position'] }}</td>
                            <td>{{ $score['name'] }}</td>
                            <td><a href="{{ route('student.result.revisi.generatePdf', ['type' => 'thesis', 'id' => $score['id']]) }}" class="btn btn-primary btn-sm">Download</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@else
    <x-card title="Perbaikan Ujian Tugas Akhir">
        <p class="text-center">Data tidak ditemukan.</p>
    </x-card>
@endif
@endsection