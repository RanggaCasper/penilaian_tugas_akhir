
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>

    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Platform digital Politeknik Negeri Bali untuk mengelola penilaian tugas akhir secara efisien dan transparan, dengan fitur pendaftaran, jadwal ujian, penugasan dosen, hingga rekap nilai, mendukung kolaborasi dan standar akademik." name="description" />
    <meta content="RanggaCasper" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <nav class="navbar navbar-expand-lg navbar-landing fixed-top" id="navbar">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <div class="gap-2 d-flex align-items-center">
                        <img src="https://www.pnb.ac.id/img/logo-pnb.3aae610b.png" alt="Logo" height="32">
                        <div class="flex-grow-2">
                            <h6 class="p-0 m-0 text-base fw-bolder">POLITEKNIK NEGERI BALI</h6>
                            <h6 class="p-0 m-0">{{ config('app.name') }}</h6>
                        </div>
                    </div>
                </a>
                <button class="py-0 navbar-toggler fs-20 text-body" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="mdi mdi-menu"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="mx-auto mt-2 navbar-nav mt-lg-0" id="navbar-example">
                        <li class="nav-item">
                            <a class="nav-link active" href="#hero">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Fitur</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#team">Tim Pengembang</a>
                        </li>
                    </ul>

                    <div class="">
                        @auth
                            @php
                                $role = strtolower(auth()->user()->role->name);
                            @endphp
                            <a href="{{ route("{$role}.dashboard") }}" class="btn btn-primary">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">Masuk</a>
                        @endauth
                    </div>
                </div>

            </div>
        </nav>
        <!-- end navbar -->
        <div class="vertical-overlay" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent.show"></div>

        <!-- start hero section -->
        <section class="pb-0 section hero-section" id="hero">
            <div class="bg-overlay bg-overlay-pattern"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-sm-10">
                        <div class="pt-5 text-center mt-lg-5">
                            <h1 class="mb-3 display-6 fw-semibold lh-base">Sistem Manajemen <span class="text-success">{{ config('app.name') }} </span></h1>
                            <p class="lead text-muted lh-base">Platform digital Politeknik Negeri Bali untuk mengelola penilaian tugas akhir secara efisien dan transparan, dengan fitur pendaftaran, jadwal ujian, penugasan dosen, hingga rekap nilai, mendukung kolaborasi dan standar akademik.</p>

                            <div class="gap-2 mt-4 d-flex justify-content-center">
                                @auth
                                    @php
                                        $role = strtolower(auth()->user()->role->name);
                                    @endphp
                                    <a href="{{ route("{$role}.dashboard") }}" class="btn btn-primary">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary">Masuk</a>
                                @endauth
                            </div>
                        </div>

                        <div class="mt-4 mt-sm-5 pt-sm-5 mb-sm-n5 demo-carousel">
                            <div class="demo-img-patten-top d-none d-sm-block">
                                <img src="assets/images/landing/img-pattern.png" class="d-block img-fluid" alt="...">
                            </div>
                            <div class="demo-img-patten-bottom d-none d-sm-block">
                                <img src="assets/images/landing/img-pattern.png" class="d-block img-fluid" alt="...">
                            </div>
                            <div class="carousel slide carousel-fade" data-bs-ride="carousel">
                                <div class="p-2 bg-white rounded shadow-lg carousel-inner">
                                    <div class="carousel-item active" data-bs-interval="2000">
                                        <img src="assets/images/landing/carousel-1.png" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item" data-bs-interval="2000">
                                        <img src="assets/images/landing/carousel-2.png" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item" data-bs-interval="2000">
                                        <img src="assets/images/landing/carousel-3.png" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item" data-bs-interval="2000">
                                        <img src="assets/images/landing/carousel-4.png" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item" data-bs-interval="2000">
                                        <img src="assets/images/landing/carousel-5.png" class="d-block w-100" alt="...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
          
            <!-- end shape -->
        </section>
        <!-- end hero section -->

        <!-- start features -->
        <section class="section" id="features">
            <div class="container">
                <div class="mt-5 row align-items-center pt-lg-5 gy-4">
                    <div class="mx-auto col-lg-6 col-sm-7 col-10">
                        <div>
                            <img src="assets/images/landing/result.png" alt="" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-muted ps-lg-5">
                            <h5 class="fs-12 text-uppercase text-success">Terstruktur</h5>
                            <h4 class="mb-3">Terdokumentasi dengan Baik</h4>
                            <p class="mb-4">Digunakan untuk menjelaskan unsur-unsur penilaian yang telah ditentukan dengan jelas berdasarkan bobot dan skor yang diberikan, sehingga hasilnya transparan dan dapat dipertanggungjawabkan.</p>

                            <div class="gap-2 vstack">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs icon-effect">
                                            <div class="bg-transparent avatar-title text-success rounded-circle h2">
                                                <i class="ri-check-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0">Penampilan/Presentasi</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs icon-effect">
                                            <div class="bg-transparent avatar-title text-success rounded-circle h2">
                                                <i class="ri-check-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0">Kemampuan Akademis</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs icon-effect">
                                            <div class="bg-transparent avatar-title text-success rounded-circle h2">
                                                <i class="ri-check-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0">Perencanaan/Analisa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end features -->
        
        <!-- start team -->
        <section class="section bg-light" id="team">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="mb-5 text-center">
                            <h3 class="mb-3 fw-semibold">Tim <span class="text-danger">Pengembang</span></h3>
                            <p class="mb-4 text-muted ff-secondary"><i class="me-2">"Berusaha untuk tidak menjadi orang sukses, tetapi lebih baik berusaha untuk menjadi orang yang bernilai."</i>- Albert Einstein.</p>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="d-flex align-items-center">

                </div>
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="p-4 text-center card-body">
                                <div class="mx-auto mb-4 avatar-xl position-relative">
                                    <img src="https://avatars.githubusercontent.com/u/76829603?v=4" alt="" class="img-fluid rounded-circle">
                                    <a href="https://github.com/RanggaCasper" class="bottom-0 btn btn-success btn-sm position-absolute end-0 rounded-circle avatar-xs">
                                        <div class="bg-transparent avatar-title">
                                            <i class="align-bottom ri-github-fill"></i>
                                        </div>
                                    </a>
                                </div>
                                <!-- end card body -->
                                <h5 class="mb-1"><a href="https://github.com/RanggaCasper" class="text-body">M. Irfan Rangganata</a></h5>
                                <p class="mb-0 text-muted ff-secondary">Programmer</p>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="p-4 text-center card-body">
                                <div class="mx-auto mb-4 avatar-xl position-relative">
                                    <img src="/assets/images/users/avatar-1.jpg" alt="" class="img-fluid rounded-circle">
                                    <a href="#" class="bottom-0 btn btn-success btn-sm position-absolute end-0 rounded-circle avatar-xs">
                                        <div class="bg-transparent avatar-title">
                                            <i class="align-bottom ri-github-fill"></i>
                                        </div>
                                    </a>
                                </div>
                                <!-- end card body -->
                                <h5 class="mb-1 text-truncate"><a href="#" class="text-body">Nyoman Agus Mahardiputra</a></h5>
                                <p class="mb-0 text-muted ff-secondary">Documenter & Supporting</p>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="p-4 text-center card-body">
                                <div class="mx-auto mb-4 avatar-xl position-relative">
                                    <img src="/assets/images/users/avatar-1.jpg" alt="" class="img-fluid rounded-circle">
                                    <a href="#" class="bottom-0 btn btn-success btn-sm position-absolute end-0 rounded-circle avatar-xs">
                                        <div class="bg-transparent avatar-title">
                                            <i class="align-bottom ri-github-fill"></i>
                                        </div>
                                    </a>
                                </div>
                                <!-- end card body -->
                                <h5 class="mb-1 text-truncate"><a href="#" class="text-body">Ida Bagus Putu Wibawa</a></h5>
                                <p class="mb-0 text-muted ff-secondary">Documenter & Supporting</p>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="p-4 text-center card-body">
                                <div class="mx-auto mb-4 avatar-xl position-relative">
                                    <img src="/assets/images/users/avatar-1.jpg" alt="" class="img-fluid rounded-circle">
                                    <a href="#" class="bottom-0 btn btn-success btn-sm position-absolute end-0 rounded-circle avatar-xs">
                                        <div class="bg-transparent avatar-title">
                                            <i class="align-bottom ri-github-fill"></i>
                                        </div>
                                    </a>
                                </div>
                                <!-- end card body -->
                                <h5 class="mb-1 text-truncate"><a href="#" class="text-body">Yohannes Putu Alvin Sutrisna</a></h5>
                                <p class="mb-0 text-muted ff-secondary">Documenter & Supporting</p>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
            </div>
            <!-- end container -->
        </section>
        <!-- end team -->

        <!-- Start footer -->
        <footer class="py-5 custom-footer bg-dark position-relative">
            <div class="container">
                <div class="row">
                    <div class="mt-4 col-lg-4">
                        <div>
                            <div>
                                <div class="gap-2 d-flex align-items-center">
                                    <img src="https://www.pnb.ac.id/img/logo-pnb.3aae610b.png" alt="Logo" height="32">
                                    <div class="flex-grow-2">
                                        <h6 class="p-0 m-0 text-light fw-bolder">POLITEKNIK NEGERI BALI</h6>
                                        <h6 class="p-0 m-0 opacity-75 text-light">{{ config('app.name') }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 fs-13">
                                <p class="ff-secondary">Platform digital Politeknik Negeri Bali untuk mengelola penilaian tugas akhir secara efisien dan transparan, dengan fitur pendaftaran, jadwal ujian, penugasan dosen, hingga rekap nilai, mendukung kolaborasi dan standar akademik.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 ms-lg-auto">
                        <div class="row">
                            <div class="mt-4 col-sm-4">
                                <h5 class="mb-0 text-white">Tautan</h5>
                                <div class="mt-3 text-muted">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li><a href="https://www.pnb.ac.id/">Politeknik Negeri Bali</a></li>
                                        <li><a href="https://sion.pnb.ac.id/">Sion</a></li>
                                        <li><a href="https://elearning.pnb.ac.id/">E-Learning</a></li>
                                        <li><a href="https://lab-jti.pnb.ac.id/">Lab JTI</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-4 col-sm-4">
                                <h5 class="mb-0 text-white">Sosial Media</h5>
                                <div class="mt-3 text-muted">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li><a href="https://www.youtube.com/channel/UCp5kuekAFrF3OBeyqO5Xl1w"><i class="ri ri-youtube-fill me-2"></i>Youtube</a></li>
                                        <li><a href="https://www.instagram.com/teknologiinformasi.pnb/?hl=en"><i class="ri ri-facebook-fill me-2"></i>Facebook</a></a></li>
                                        <li><a href="https://www.facebook.com/profile.php?id=61555520697261"><i class="ri ri-instagram-fill me-2"></i>Instagram</a></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-4 col-sm-4">
                                <h5 class="mb-0 text-white">Kontak</h5>
                                <div class="mt-3 text-muted">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li><a href="mailto:mirangganata@gmail.com"><i class="ri ri-mail-fill me-2"></i>mirangganata@gmail.com</a></li>
                                        <li><a href="https://wa.me/6283189944777"><i class="ri ri-phone-fill me-2"></i>083189944777</a></li>
                                        <li><a href="https://maps.app.goo.gl/WDJE9Bf3vJ3wuDPx9"><i class="ri ri-map-pin-fill me-2"></i>Kampus Politeknik Negeri Bali, Bukit Jimbaran, Kuta Selatan, Badung - Bali 80361
                                            PO BOX 1064 Tuban</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-5 text-center row text-sm-start align-items-center">
                    <div class="col-sm-6">

                        <div>
                            <p class="mb-0 copy-rights">
                                <script> document.write(new Date().getFullYear()) </script> © {{ config('app.name') }} - Politeknik Negeri Bali
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mt-3 text-sm-end mt-sm-0">
                            <ul class="mb-0 list-inline footer-social-link">
                                <li class="list-inline-item">
                                    <a href="https://facebook.com/hyfan.gt" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-facebook-fill"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://github.com/RanggaCasper" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-github-fill"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="mailto:mirangganata@gmail.com" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-google-fill"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://instagram.com/ranggacasper_" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-instagram-line"></i>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end footer -->


        <!--start back-to-top-->
        <button onclick="topFunction()" class="btn btn-danger btn-icon landing-back-top" id="back-to-top">
            <i class="ri-arrow-up-line"></i>
        </button>
        <!--end back-to-top-->

    </div>
    <!-- end layout wrapper -->


    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- landing init -->
    <script src="assets/js/pages/landing.init.js"></script>
</body>

</html>