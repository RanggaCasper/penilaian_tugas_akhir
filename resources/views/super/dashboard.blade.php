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
                <h3 class="mb-2"><span>{{ App\Models\User::get()->where('role_id',4)->count() }}</span></h3>
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
                <h3 class="mb-2"><span>{{ App\Models\User::get()->where('role_id', 3)->count() }}</span></h3>
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
                <h3 class="mb-2"><span>{{ App\Models\User::get()->where('role_id', 2)->count() }}</span></h3>
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
@endsection