@extends('layouts.auth')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="mt-4 card">
            <div class="p-4 card-body">
                <div class="mt-2 text-center">
                    <h5 class="text-primary">Selamat Datang!</h5>
                    <p class="text-muted">Masuk untuk memulai session di {{ config('app.name') }}.</p>
                </div>
                <div class="p-2 mt-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <x-input-field label="Username" type="text" name="username" id="username" />
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="password-input">Password</label>
                            <div class="mb-3 position-relative auth-pass-inputgroup">
                                <input type="password" name="password" class="form-control pe-5 password-input" placeholder="Enter password" id="password">
                                <button class="top-0 btn btn-link position-absolute end-0 text-decoration-none text-muted password-addon" type="button" id="password-addon">
                                    <i class="align-middle ri-eye-fill"></i>
                                </button>
                                <span class="mt-1 message-error text-danger"></span>
                            </div>
                        </div>                        

                        <div class="mt-4">
                            <x-button type="submit" class="btn btn-primary w-100" label="Submit" />
                        </div>
                    </form>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->

        <div class="mt-4 text-center">
            <p class="mb-0">Tidak memiliki akun ? <a href="#" class="fw-semibold text-primary text-decoration-underline"> Daftar </a> </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/action.js') }}"></script>
@endpush
