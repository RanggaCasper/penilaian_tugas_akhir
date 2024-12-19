<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
// User
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\Register\RegisterFinalProjectController;

// Admin
use App\Http\Controllers\Admin\Lecturer\LecturerController;
use App\Http\Controllers\Admin\EvaluationCritariaController;
use App\Http\Controllers\Admin\Proposal\ProposalPeriodController;
use App\Http\Controllers\Admin\FinalProject\FinalProjectPeriodController;
use App\Http\Controllers\Admin\Student\StudentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::prefix('auth')->middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('auth/logout', [LoginController::class, 'logout'])->middleware('auth')->name('auth.logout');

Route::prefix('admin')->as('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::prefix('periode')->as('periode.')->group(function () {
        Route::prefix('proposal')->as('proposal.')->group(function () {
            Route::get('/', [ProposalPeriodController::class, 'index'])->name('index');
            Route::post('/', [ProposalPeriodController::class, 'store'])->name('store');
            Route::get('/get', [ProposalPeriodController::class, 'get'])->name('get');
            Route::get('/get/{id}', [ProposalPeriodController::class, 'getById'])->name('getById');
            Route::put('{id}', [ProposalPeriodController::class, 'update'])->name('update');
            Route::delete('{id}', [ProposalPeriodController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('final-project')->as('final_project.')->group(function () {
            Route::get('/', [FinalProjectPeriodController::class, 'index'])->name('index');
            Route::post('/', [FinalProjectPeriodController::class, 'store'])->name('store');
            Route::get('/get', [FinalProjectPeriodController::class, 'get'])->name('get');
            Route::get('/get/{id}', [FinalProjectPeriodController::class, 'getById'])->name('getById');
            Route::put('{id}', [FinalProjectPeriodController::class, 'update'])->name('update');
            Route::delete('{id}', [FinalProjectPeriodController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('evaluation')->as('evaluation.')->group(function () {
        Route::prefix('criteria')->as('criteria.')->group(function () {
            Route::get('/', [EvaluationCritariaController::class, 'index'])->name('index');
            Route::post('/', [EvaluationCritariaController::class, 'store'])->name('store');
            Route::get('/get', [EvaluationCritariaController::class, 'get'])->name('get');
            Route::get('/get/{id}', [EvaluationCritariaController::class, 'getById'])->name('getById');
            Route::put('{id}', [EvaluationCritariaController::class, 'update'])->name('update');
            Route::delete('{id}', [EvaluationCritariaController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('student')->as('student.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/get', [StudentController::class, 'get'])->name('get');
        Route::get('/get-data', [StudentController::class, 'getData'])->name('getData');
    });
    
    Route::prefix('lecturer')->as('lecturer.')->group(function () {
        Route::get('/', [LecturerController::class, 'index'])->name('index');
        Route::post('/', [LecturerController::class, 'store'])->name('store');
        Route::get('/get', [LecturerController::class, 'get'])->name('get');
        Route::get('/get/{id}', [LecturerController::class, 'getById'])->name('getById');
        Route::put('{id}', [LecturerController::class, 'update'])->name('update');
        Route::delete('{id}', [LecturerController::class, 'destroy'])->name('destroy');
    });
});

Route::prefix('student')->as('student.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('register')->as('register.')->group(function () {
        Route::prefix('final-project')->as('final_project.')->group(function () {
            Route::get('/', [RegisterFinalProjectController::class, 'index'])->name('index');
            Route::post('/', [RegisterFinalProjectController::class, 'store'])->name('store');
            Route::put('/', [RegisterFinalProjectController::class, 'update'])->name('update');
        });
    });
});