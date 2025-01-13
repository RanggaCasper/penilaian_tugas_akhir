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
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proposal_score['scores'] as $score)
                        <tr>
                            <td>{{ $score['position'] }}</td>
                            <td>{{ $score['name'] }}</td>
                            <td>{{ number_format($score['score']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data Nilai untuk proposal.</td>
                        </tr>
                    @endforelse
                    @if (!empty($proposal_score['average_score']))
                        <tr>
                            <td colspan="2" class="text-center"><strong>Rata - Rata</strong></td>
                            <td><strong>{{ number_format($proposal_score['average_score']) }}</strong></td>
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

@if (!empty($thesis_score['scores']))
    <x-card title="Hasil Ujian Tugas Akhir">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Posisi Penguji</th>
                        <th>Nama Penguji</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($thesis_score['scores'] as $score)
                        <tr>
                            <td>{{ $score['position'] }}</td>
                            <td>{{ $score['name'] }}</td>
                            <td>{{ number_format($score['score']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                    @if (!empty($thesis_score['average_score']))
                        <tr>
                            <td colspan="2" class="text-center"><strong>Rata - Rata</strong></td>
                            <td><strong>{{ number_format($thesis_score['average_score']) }}</strong></td>
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

@if (!empty($guidance_score['scores']))
    <x-card title="Hasil Ujian Tugas Akhir">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Posisi Pembimbing</th>
                        <th>Nama Pembimbing</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guidance_score['scores'] as $score)
                        <tr>
                            <td>{{ $score['position'] }}</td>
                            <td>{{ $score['name'] }}</td>
                            <td>{{ number_format($score['score']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                    @if (!empty($guidance_score['average_score']))
                        <tr>
                            <td colspan="2" class="text-center"><strong>Rata - Rata</strong></td>
                            <td><strong>{{ number_format($guidance_score['average_score']) }}</strong></td>
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

@if (!empty($final_score))
    <x-card title="Nilai Akhir">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Komponen</th>
                        <th>Bobot</th>
                        <th>Nilai</th>
                        <th>Bobot X Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Nilai Proposal</td>
                        <td>10%</td>
                        <td>{{ number_format($final_score['proposal_score']['average_score']) }}</td>
                        <td>{{ number_format($final_score['proposal_score']['final_score']) }}</td>
                    </tr>
                    <tr>
                        <td>Nilai Tugas Akhir</td>
                        <td>30%</td>
                        <td>{{ number_format($final_score['thesis_score']['average_score']) }}</td>
                        <td>{{ number_format($final_score['thesis_score']['final_score']) }}</td>
                    </tr>
                    @if (!empty($final_score['guidance_score']))
                        @foreach ($final_score['guidance_score']['scores'] as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item['position'] }}</strong>
                                    <br>{{ $item['name'] }}
                                </td>
                                <td>30%</td>
                                <td>{{ number_format($item['score']) }}</td>
                                <td>{{ number_format($item['final_score']) }}</td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td colspan="3" class="text-center"><strong>Nilai Akhir</strong></td>
                        <td><strong>{{ number_format($final_score['total_score']) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
@endif
@endsection
