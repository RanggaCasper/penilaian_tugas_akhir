<div class="navbar-header">
    <div class="d-flex">
        <!-- LOGO -->
        <div class="navbar-brand-box horizontal-logo">
            <a href="index.html" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="https://caspertopup.com/storage/img_url/2MBj1cRyplt164mLn45aW1NFtg1o42OTfiwzihQR.png" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="https://caspertopup.com/storage/img_url/2MBj1cRyplt164mLn45aW1NFtg1o42OTfiwzihQR.png" alt="" height="17">
                </span>
            </a>

            <a href="index.html" class="logo logo-light">
                <span class="logo-sm">
                    <img src="https://caspertopup.com/storage/img_url/2MBj1cRyplt164mLn45aW1NFtg1o42OTfiwzihQR.png" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="https://caspertopup.com/storage/img_url/2MBj1cRyplt164mLn45aW1NFtg1o42OTfiwzihQR.png" alt="" height="17">
                </span>
            </a>
        </div>

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
                    <img class="rounded-circle header-profile-user" src="{{ Storage::url(auth()->user()->profile_image) }}" alt="Header Avatar">
                    <span class="text-start ms-xl-2">
                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ auth()->user()->username }}</span>
                        <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Rp. {{ number_format(auth()->user()->balance, 0,',','.') }}</span>
                    </span>
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <!-- item-->
                <h6 class="dropdown-header">Hi!, {{ auth()->user()->username }}</h6>
                
                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item"><i class="align-middle mdi mdi-logout text-muted fs-16 me-1"></i> <span class="align-middle" data-key="t-logout">Keluar</span></button>
                </form>
            </div>
        </div>
    </div>
</div>