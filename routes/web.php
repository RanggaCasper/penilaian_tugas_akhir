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
            Route::controller(\App\Http\Controllers\Admin\Proposal\PeriodController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });
    });
    
    // Final Project
    Route::prefix('final-project')->as('final_project.')->group(function () {
        Route::prefix('period')->as('period.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\FinalProject\PeriodController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });

        Route::prefix('register')->as('register.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\FinalProject\RegisterController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });

        Route::prefix('schedule')->as('schedule.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\FinalProject\ScheduleController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/student', 'getStudent')->name('getStudent');
                Route::get('/get/examiner', 'getExaminer')->name('getExaminer');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });
    });

    // Evaluation
    Route::prefix('evaluation')->as('evaluation.')->group(function () {
        Route::controller(\App\Http\Controllers\Admin\Evaluation\EvaluationController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/get', 'get')->name('get');
            Route::get('/get/{id}', 'getById')->name('getById');
            Route::put('{id}', 'update')->name('update');
            Route::delete('{id}', 'destroy')->name('destroy');
        });

        Route::prefix('criteria')->as('criteria.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Evaluation\CriteriaController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });

            Route::prefix('sub')->as('sub.')->group(function () {
                Route::controller(\App\Http\Controllers\Admin\Evaluation\SubCriteriaController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/', 'store')->name('store');
                    Route::get('/get', 'get')->name('get');
                    Route::get('/get/{id}', 'getById')->name('getById');
                    Route::put('{id}', 'update')->name('update');
                    Route::delete('{id}', 'destroy')->name('destroy');
                });
            });
        });
    });

    // Student
    Route::prefix('student')->as('student.')->group(function () {
        Route::controller(\App\Http\Controllers\Admin\Student\StudentController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get', 'get')->name('get');
            Route::get('/get-data', 'getData')->name('getData');
        });
    });

    // Lecturer
    Route::prefix('lecturer')->as('lecturer.')->group(function () {
        Route::controller(\App\Http\Controllers\Admin\Lecturer\LecturerController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/get', 'get')->name('get');
            Route::get('/get-data', 'getData')->name('getData');
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
            Route::controller(\App\Http\Controllers\Student\FinalProject\RegisterController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/', 'update')->name('update');
            });
        });
    });
});
