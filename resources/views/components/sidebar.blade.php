<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('home') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="https://www.pnb.ac.id/img/logo-pnb.3aae610b.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <div class="gap-2 mt-3 d-flex align-items-center">
                    <img src="https://www.pnb.ac.id/img/logo-pnb.3aae610b.png" alt="Logo" height="32">
                    <div class="text-white flex-grow-2">
                        <h6 class="p-0 m-0 text-white opacity-75 fw-bolder">POLITEKNIK NEGERI BALI</h6>
                        <h6 class="p-0 m-0 text-white opacity-50">{{ config('app.name') }}</h6>
                    </div>
                </div>
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('home') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="https://www.pnb.ac.id/img/logo-pnb.3aae610b.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <div class="gap-2 mt-3 d-flex align-items-center">
                    <img src="https://www.pnb.ac.id/img/logo-pnb.3aae610b.png" alt="Logo" height="32">
                    <div class="text-white flex-grow-2">
                        <h6 class="p-0 m-0 text-white opacity-75 fw-bolder">POLITEKNIK NEGERI BALI</h6>
                        <h6 class="p-0 m-0 text-white opacity-50">{{ config('app.name') }}</h6>
                    </div>
                </div>
            </span>
        </a>
        <button type="button" class="p-0 btn btn-sm fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <x-menu-title title="Menu" />
                @if (auth()->user()->role->name == "Super")
                    <x-navlink icon="ri-dashboard-line" title="Dashboard" href="{{ route('super.dashboard') }}" active="{{ request()->routeIs('super.dashboard') }}" />
                    <x-menu-title title="Pengguna" />
                        <x-navlink icon="ri-user-line" title="Kelola Mahasiswa" href="{{ route('super.student.index') }}" active="{{ request()->routeIs('super.student.index') }}" />
                        <x-navlink icon="ri-user-line" title="Kelola Dosen" href="{{ route('super.lecturer.index') }}" active="{{ request()->routeIs('super.lecturer.index') }}" />
                            
                @elseif (auth()->user()->role->name == "Admin")
                    <x-navlink icon="ri-dashboard-line" title="Dashboard" href="{{ route('admin.dashboard') }}" active="{{ request()->routeIs('admin.dashboard') }}" />
                    <x-menu-title title="Pengguna" />
                        <x-navlink icon="ri-user-line" title="Data Mahasiswa" href="{{ route('admin.student.index') }}" active="{{ request()->routeIs('admin.student.index') }}" />
                        <x-navlink icon="ri-user-line" title="Data Dosen" href="{{ route('admin.lecturer.index') }}" active="{{ request()->routeIs('admin.lecturer.index') }}" />
                    <x-menu-title title="Periode" />
                        <x-navlink icon="ri-calendar-event-line" title="Periode Ujian" href="{{ route('admin.period.index') }}" active="{{ request()->routeIs('admin.period.index') }}" />
                    <x-menu-title title="Proposal" />
                        <x-navlink icon="ri-user-add-line" title="Kelola Pendaftaran" href="{{ route('admin.proposal.register.index') }}" active="{{ request()->routeIs('admin.proposal.register.index') }}" />
                        <x-navlink icon="ri-calendar-event-line" title="Jadwal Ujian" href="{{ route('admin.proposal.schedule.index') }}" active="{{ request()->routeIs('admin.proposal.schedule.index') }}" />
                    <x-menu-title title="Tugas Akhir" />
                        <x-navlink icon="ri-user-add-line" title="Kelola Pendaftaran" href="{{ route('admin.final_project.register.index') }}" active="{{ request()->routeIs('admin.final_project.register.index') }}" />
                        <x-navlink icon="ri-calendar-event-line" title="Jadwal Ujian" href="{{ route('admin.final_project.schedule.index') }}" active="{{ request()->routeIs('admin.final_project.schedule.index') }}" />
                    <x-menu-title title="Rubrik Penilaian" />
                        <x-navlink icon="ri-file-edit-line" title="Rubrik" href="{{ route('admin.rubric.index') }}" active="{{ request()->routeIs('admin.rubric.index') }}" />
                        <x-navlink icon="ri-file-edit-line" title="Kriteria" href="{{ route('admin.rubric.criteria.index') }}" active="{{ request()->routeIs('admin.rubric.criteria.index') }}" />
                        <x-navlink icon="ri-file-edit-line" title="Sub Kriteria" href="{{ route('admin.rubric.criteria.sub.index') }}" active="{{ request()->routeIs('admin.rubric.criteria.sub.index') }}" />
                @elseif (auth()->user()->role->name == "Lecturer")
                    <x-navlink icon="ri-dashboard-line" title="Dashboard" href="{{ route('lecturer.dashboard') }}" active="{{ request()->routeIs('lecturer.dashboard') }}" />
                    <x-menu-title title="Proposal" />
                        <x-navlink icon="ri-calendar-event-line" title="Jadwal Ujian" href="{{ route('lecturer.proposal.schedule.index') }}" active="{{ request()->routeIs('lecturer.proposal.schedule.index') }}" />
                        <x-navlink icon="ri-file-edit-line" title="Penilaian Ujian" href="{{ route('lecturer.proposal.exam.index') }}" active="{{ request()->routeIs('lecturer.proposal.exam.index') }}" />
                    <x-menu-title title="Tugas Akhir" />
                        <x-navlink icon="ri-calendar-event-line" title="Jadwal Ujian" href="{{ route('lecturer.final_project.schedule.index') }}" active="{{ request()->routeIs('lecturer.final_project.schedule.index') }}" />
                        <x-navlink icon="ri-file-edit-line" title="Penilaian Ujian" href="{{ route('lecturer.final_project.exam.index') }}" active="{{ request()->routeIs('lecturer.final_project.exam.index') }}" />
                            
                @elseif (auth()->user()->role->name == "Student")
                    @php
                        $checkFinalProject = App\Models\FinalProject\FinalProject::where('student_id', Auth::id())
                            ->where('status', 'disetujui')
                            ->exists();
                        $checkProposal = App\Models\Proposal\Proposal::where('student_id', Auth::id())
                            ->where('status', 'disetujui')
                            ->exists();
                    @endphp
                            
                    <x-navlink icon="ri-dashboard-line" title="Dashboard" href="{{ route('student.dashboard') }}" active="{{ request()->routeIs('student.dashboard') }}" />
                    <x-menu-title title="Proposal" />
                        <x-navlink icon="ri-user-add-line" title="Pendaftaran" href="{{ route('student.proposal.register.index') }}" active="{{ request()->routeIs('student.proposal.register.index') }}" />
                        @if ($checkProposal)  
                            <x-navlink icon="ri-calendar-event-line" title="Jadwal Ujian" href="{{ route('student.proposal.schedule.index') }}" active="{{ request()->routeIs('student.proposal.schedule.index') }}" />
                        @endif
                    <x-menu-title title="Tugas Akhir" />
                        <x-navlink icon="ri-user-add-line" title="Pendaftaran" href="{{ route('student.final_project.register.index') }}" active="{{ request()->routeIs('student.final_project.register.index') }}" />
                        @if ($checkFinalProject)  
                            <x-navlink icon="ri-calendar-event-line" title="Jadwal Ujian" href="{{ route('student.final_project.schedule.index') }}" active="{{ request()->routeIs('student.final_project.schedule.index') }}" />
                        @endif
                    <x-menu-title title="Hasil" />
                        <x-navlink icon="ri-user-add-line" title="Hasil Ujian" href="{{ route('student.result.index') }}" active="{{ request()->routeIs('student.result.index') }}" />
                @elseif (auth()->user()->role->name == "Special")
                    <x-navlink icon="ri-dashboard-line" title="Dashboard" href="{{ route('special.dashboard') }}" active="{{ request()->routeIs('special.dashboard') }}" />
                    <x-menu-title title="Api" />
                        <x-navlink icon="ri-key-2-line" title="Pengaturan" href="{{ route('special.api.setting') }}" active="{{ request()->routeIs('special.api.setting') }}" />
                        <x-navlink icon="ri-file-line" title="Dokumentasi" href="{{ route('special.api.document') }}" active="{{ request()->routeIs('special.api.document') }}" />
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
