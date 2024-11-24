<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PeriodeProposalController;

Route::prefix('auth')->as('auth.')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('auth/logout', [LoginController::class, 'logout'])->name('auth.logout');

Route::prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('periode')->as('periode.')->group(function () {
        Route::prefix('proposal')->as('proposal.')->group(function () {
            Route::get('/', [PeriodeProposalController::class, 'index'])->name('index');
            Route::post('/', [PeriodeProposalController::class, 'store'])->name('store');
            Route::get('/get', [PeriodeProposalController::class, 'get'])->name('get');
            Route::get('/get/{id}', [PeriodeProposalController::class, 'getById'])->name('getById');
            Route::put('{id}', [PeriodeProposalController::class, 'update'])->name('update');
            Route::delete('{id}', [PeriodeProposalController::class, 'destroy'])->name('destroy');
        });
    });
});