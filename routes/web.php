<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'index'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.submit');
});

Route::post('auth/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->middleware('auth')->name('auth.logout');

Route::prefix('admin')->as('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('periode')->as('periode.')->group(function () {
        Route::prefix('proposal')->as('proposal.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Proposal\ProposalPeriodController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });

        Route::prefix('final-project')->as('final_project.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\FinalProject\FinalProjectPeriodController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });
    });
    
    Route::prefix('final-project')->as('final_project.')->group(function () {
        Route::prefix('register')->as('register.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\FinalProject\FinalProjectRegisterController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });
    });

    Route::prefix('evaluation')->as('evaluation.')->group(function () {
        Route::prefix('criteria')->as('criteria.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\EvaluationCritariaController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });
    });

    Route::prefix('student')->as('student.')->group(function () {
        Route::controller(\App\Http\Controllers\Admin\Student\StudentController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get', 'get')->name('get');
            Route::get('/get-data', 'getData')->name('getData');
        });
    });

    Route::prefix('lecturer')->as('lecturer.')->group(function () {
        Route::controller(\App\Http\Controllers\Admin\Lecturer\LecturerController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/get', 'get')->name('get');
            Route::get('/get/{id}', 'getById')->name('getById');
            Route::put('{id}', 'update')->name('update');
            Route::delete('{id}', 'destroy')->name('destroy');
        });
    });
});

Route::prefix('student')->as('student.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('register')->as('register.')->group(function () {
        Route::prefix('final-project')->as('final_project.')->group(function () {
            Route::controller(\App\Http\Controllers\Student\FinalProject\FinalProjectRegisterController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/', 'update')->name('update');
            });
        });
    });
});
