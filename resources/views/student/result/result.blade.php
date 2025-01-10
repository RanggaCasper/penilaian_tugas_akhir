@extends('layouts.app')

@section('content')
@if (!empty($proposal_score['scores']))
    <x-card title="Hasil Ujian Proposal">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Posisi Penguji</th>
                        <th>Nama Penguji</th>
                        <th>Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proposal_score['scores'] as $score)
                        <tr>
                            <td>{{ $score['examiner_position'] }}</td>
                            <td>{{ $score['examiner'] }}</td>
                            <td>{{ number_format($score['score'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data skor untuk proposal.</td>
                        </tr>
                    @endforelse
                    @if (!empty($proposal_score['final_score']))
                        <tr>
                            <td colspan="2" class="text-center"><strong>Rata - Rata</strong></td>
                            <td><strong>{{ number_format($proposal_score['average_score'], 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center"><strong>Nilai Akhir</strong></td>
                            <td><strong>{{ number_format($proposal_score['final_score'], 2) }}</strong></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </x-card>
@else
    <x-card title="Hasil Ujian Proposal">
        <p class="text-center">Data tidak ditemukan.</p>
    </x-card>
@endif

@if (!empty($final_project_score['scores']))
    <x-card title="Hasil Ujian Tugas Akhir">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Posisi Penguji</th>
                        <th>Nama Penguji</th>
                        <th>Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($final_project_score['scores'] as $score)
                        <tr>
                            <td>{{ $score['examiner_position'] }}</td>
                            <td>{{ $score['examiner'] }}</td>
                            <td>{{ number_format($score['score'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data skor untuk tugas akhir.</td>
                        </tr>
                    @endforelse
                    @if (!empty($final_project_score['final_score']))
                        <tr>
                            <td colspan="2" class="text-center"><strong>Rata - Rata</strong></td>
                            <td><strong>{{ number_format($final_project_score['average_score'], 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center"><strong>Nilai Akhir</strong></td>
                            <td><strong>{{ number_format($final_project_score['final_score'], 2) }}</strong></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </x-card>
@else
    <x-card title="Hasil Ujian Tugas Akhir">
        <p class="text-center">Data tidak ditemukan.</p>
    </x-card>
@endif
@endsection
