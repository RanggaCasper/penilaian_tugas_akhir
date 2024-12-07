<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProposalPeriodController;

Route::prefix('auth')->middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('auth/logout', [LoginController::class, 'logout'])->middleware('auth')->name('auth.logout');

Route::prefix('admin')->as('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('periode')->as('periode.')->group(function () {
        Route::prefix('proposal')->as('proposal.')->group(function () {
            Route::get('/', [ProposalPeriodController::class, 'index'])->name('index');
            Route::post('/', [ProposalPeriodController::class, 'store'])->name('store');
            Route::get('/get', [ProposalPeriodController::class, 'get'])->name('get');
            Route::get('/get/{id}', [ProposalPeriodController::class, 'getById'])->name('getById');
            Route::put('{id}', [ProposalPeriodController::class, 'update'])->name('update');
            Route::delete('{id}', [ProposalPeriodController::class, 'destroy'])->name('destroy');
        });
    });
});