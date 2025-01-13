<div class="navbar-header">
    <div class="d-flex">
        <button type="button" class="px-3 btn btn-sm fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
            <span class="hamburger-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </button>
    </div>

    <div class="d-flex align-items-center">

        <div class="dropdown ms-sm-3 header-item topbar-user">
            <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="d-flex align-items-center">
                    <img class="rounded-circle header-profile-user" src="{{ auth()->user()->profile_image ? auth()->user()->profile_image : 'https://www.casperproject.my.id/storage/profile/default.jpg' }}" alt="Header Avatar">
                    <span class="text-start ms-xl-2">
                        <span class="d-inline-block ms-1 fw-medium user-name-text">{{ auth()->user()->name }}</span>
                        <span class="d-block ms-1 fs-12 user-name-sub-text">{{ auth()->user()->role->name }}</span>
                    </span>
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <!-- item-->
                <h6 class="dropdown-header">Hi!, {{ auth()->user()->name }}</h6>
                <a class="dropdown-item" href="{{ route(strtolower(auth()->user()->role->name).'.profile') }}"><i class="align-middle mdi mdi-account-circle text-muted fs-16 me-1"></i> <span class="align-middle">Profile</span></a>
                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item"><i class="align-middle mdi mdi-logout text-muted fs-16 me-1"></i> <span class="align-middle" data-key="t-logout">Keluar</span></button>
                </form>
            </div>
        </div>
    </div>
</div>