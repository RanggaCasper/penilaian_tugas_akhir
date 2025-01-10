@extends('layouts.app')

@section('content')
<div class="mt-3 row">
    <div class="col-xxl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="mb-3 d-flex">
                    <div class="flex-grow-1">
                        <lord-icon src="https://cdn.lordicon.com/kdduutaw.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"></lord-icon>
                    </div>
                </div>
                <h3 class="mb-2">
                    <span>
                        {{ App\Models\User::whereHas('role', function ($query) {
                            $query->where('name', 'Student');
                        })
                        ->count() }}
                    </span>
                </h3>
                <h6 class="mb-0 text-muted">Total Mahasiswa</h6>                
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="mb-3 d-flex">
                    <div class="flex-grow-1">
                        <lord-icon src="https://cdn.lordicon.com/kdduutaw.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"></lord-icon>
                    </div>
                </div>
                <h3 class="mb-2">
                    <span>
                        {{ App\Models\User::whereHas('role', function ($query) {
                            $query->where('name', 'Lecturer');
                        })
                        ->count() }}
                    </span>
                </h3>
                <h6 class="mb-0 text-muted">Total Dosen</h6>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="mb-3 d-flex">
                    <div class="flex-grow-1">
                        <lord-icon src="https://cdn.lordicon.com/kdduutaw.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"></lord-icon>
                    </div>
                </div>
                <h3 class="mb-2">
                    <span>
                        {{ App\Models\User::whereHas('role', function ($query) {
                            $query->where('name', 'Admin');
                        })
                        ->count() }}
                    </span>
                </h3>
                <h6 class="mb-0 text-muted">Total Admin</h6>                
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="mb-3 d-flex">
                    <div class="flex-grow-1">
                        <lord-icon src="https://cdn.lordicon.com/kdduutaw.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"></lord-icon>
                    </div>
                </div>
                <h3 class="mb-2"><span>{{ App\Models\ProgramStudy::count() }}</span></h3>
                <h6 class="mb-0 text-muted">Total Program Studi</h6>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <x-card title="Statistik Program Studi">
            <div id="programStudyChart"></div>
        </x-card>
    </div>
    <div class="col-md-6">
        <x-card title="Statistik Angkatan">
            <div id="generationChart"></div>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const dataProdi = @json($data_prodi);
    const dataGeneration = @json($data_generation);

    const programStudies = dataProdi.map(item => item.program_study);
    const userCountsProdi = dataProdi.map(item => item.total);

    const sortedGeneration = Object.values(dataGeneration).sort((a, b) => a.generation.localeCompare(b.generation));
    const generations = sortedGeneration.map(item => item.generation);
    const userCountsGeneration = sortedGeneration.map(item => item.total);

    var programStudyOptions = {
        chart: {
            type: 'pie',
            height: 370
        },
        series: userCountsProdi,
        labels: programStudies,
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var programStudyChart = new ApexCharts(document.querySelector("#programStudyChart"), programStudyOptions);
    programStudyChart.render();

    var generationOptions = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        series: [{
            name: 'Mahasiswa',
            data: userCountsGeneration,
        }],
        xaxis: {
            categories: generations
        }
    };

    var generationChart = new ApexCharts(document.querySelector("#generationChart"), generationOptions);
    generationChart.render();
</script>
@endpush