<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
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
                @if (auth()->user()->role->name == "Admin")
                    <x-navlink icon="ri-dashboard-line" title="Dashboard" href="{{ route('admin.dashboard') }}" active="{{ request()->routeIs('admin.dashboard') }}" />
                    <x-menu-title title="Mahasiswa" />
                        <x-navlink icon="ri-user-line" title="Kelola Mahasiswa" href="{{ route('admin.student.index') }}" active="{{ request()->routeIs('admin.student.index') }}" />
                    <x-menu-title title="Dosen" />
                        <x-navlink icon="ri-user-line" title="Kelola Dosen" href="{{ route('admin.lecturer.index') }}" active="{{ request()->routeIs('admin.lecturer.index') }}" />
                    <x-menu-title title="Proposal" />
                        {{-- <x-navlink icon="ri-calendar-event-line" title="Periode Ujian Proposal" href="{{ route('admin.periode.proposal.index') }}" active="{{ request()->routeIs('admin.periode.proposal.index') }}" /> --}}
                    <x-menu-title title="Tugas Akhir" />
                        <x-navlink icon="ri-calendar-event-line" title="Periode Tugas Akhir" href="{{ route('admin.final_project.period.index') }}" active="{{ request()->routeIs('admin.final_project.period.index') }}" />
                        <x-navlink icon="ri-user-add-line" title="Kelola Pendaftaran" href="{{ route('admin.final_project.register.index') }}" active="{{ request()->routeIs('admin.final_project.register.index') }}" />
                        <x-navlink icon="ri-calendar-event-line" title="Jadwal Tugas Akhir" href="{{ route('admin.final_project.schedule.index') }}" active="{{ request()->routeIs('admin.final_project.schedule.index') }}" />
                    <x-menu-title title="Penilaian" />
                        <x-navlink icon="ri-calendar-event-line" title="Penilaian" href="{{ route('admin.evaluation.index') }}" active="{{ request()->routeIs('admin.evaluation.index') }}" />
                        <x-navlink icon="ri-calendar-event-line" title="Kriteria Penilaian" href="{{ route('admin.evaluation.criteria.index') }}" active="{{ request()->routeIs('admin.evaluation.criteria.index') }}" />
                        <x-navlink icon="ri-calendar-event-line" title="Sub Kriteria Penilaian" href="{{ route('admin.evaluation.criteria.sub.index') }}" active="{{ request()->routeIs('admin.evaluation.criteria.sub.index') }}" />
                @elseif (auth()->user()->role->name == "Student")
                    <x-navlink icon="ri-dashboard-line" title="Dashboard" href="{{ route('student.dashboard') }}" active="{{ request()->routeIs('student.dashboard') }}" />
                    <x-menu-title title="Tugas Akhir" />
                        <x-navlink icon="ri-user-line" title="Daftar Tugas Akhir" href="{{ route('student.register.final_project.index') }}" active="{{ request()->routeIs('student.register.final_project.index') }}" />
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
