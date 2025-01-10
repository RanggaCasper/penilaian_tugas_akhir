<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::prefix('auth')->middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'index'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.submit');
});

Route::post('auth/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->middleware('auth')->name('auth.logout');

// Super Admin
Route::prefix('super')->as('super.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Super\DashboardController::class, 'index'])->name('dashboard');

    // Mahasiswa
    Route::prefix('student')->as('student.')->group(function () {
        Route::controller(\App\Http\Controllers\Super\Student\StudentController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get', 'get')->name('get');
            Route::post('/get-data', 'getData')->name('getData');
        });
    });

    // Dosen
    Route::prefix('lecturer')->as('lecturer.')->group(function () {
        Route::controller(\App\Http\Controllers\Super\Lecturer\LecturerController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/get', 'get')->name('get');
            Route::post('/get-data', 'getData')->name('getData');
            Route::get('/get/{id}', 'getById')->name('getById');
            Route::put('{id}', 'update')->name('update');
            Route::delete('{id}', 'destroy')->name('destroy');
        });
    });
});

Route::prefix('admin')->as('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Periode Ujian
    Route::prefix('period')->as('period.')->group(function () {
        Route::controller(\App\Http\Controllers\Admin\Period\PeriodController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/get', 'get')->name('get');
            Route::get('/get/{id}', 'getById')->name('getById');
            Route::put('{id}', 'update')->name('update');
            Route::delete('{id}', 'destroy')->name('destroy');
        });
    });

    // Proposal
    Route::prefix('proposal')->as('proposal.')->group(function () {
        // Pendaftaran
        Route::prefix('register')->as('register.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Proposal\RegisterController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::get('/mentor/get', 'getMentor')->name('getMentor');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });

        // Jadwal
        Route::prefix('schedule')->as('schedule.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Proposal\ScheduleController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/student', 'getStudent')->name('getStudent');
                Route::get('/get/rubric', 'getRubric')->name('getRubric');
                Route::get('/get/examiner', 'getExaminer')->name('getExaminer');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });
    });

    // Tugas Akhir
    Route::prefix('final-project')->as('final_project.')->group(function () {
        // Pendaftaran
        Route::prefix('register')->as('register.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\FinalProject\RegisterController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });

        // Jadwal
        Route::prefix('schedule')->as('schedule.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\FinalProject\ScheduleController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/student', 'getStudent')->name('getStudent');
                Route::get('/get/rubric', 'getRubric')->name('getRubric');
                Route::get('/get/examiner', 'getExaminer')->name('getExaminer');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });
    });

    // Rubrik Penilaian
    Route::prefix('rubric')->as('rubric.')->group(function () {
        // Rubrik Penilaian
        Route::controller(\App\Http\Controllers\Admin\Rubric\RubricController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/get', 'get')->name('get');
            Route::get('/get/{id}', 'getById')->name('getById');
            Route::put('{id}', 'update')->name('update');
            Route::delete('{id}', 'destroy')->name('destroy');
        });

        // Kriteria Penilaian
        Route::prefix('criteria')->as('criteria.')->group(function () {
            // Kriteria
            Route::controller(\App\Http\Controllers\Admin\Rubric\CriteriaController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });

            // Sub Kriteria
            Route::prefix('sub')->as('sub.')->group(function () {
                Route::controller(\App\Http\Controllers\Admin\Rubric\SubCriteriaController::class)->group(function () {
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

    // Mahasiswa
    Route::prefix('student')->as('student.')->group(function () {
        Route::controller(\App\Http\Controllers\Admin\Student\StudentController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get', 'get')->name('get');
            Route::post('/get-data', 'getData')->name('getData');
        });
    });

    // Dosen
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

    // Proposal
    Route::prefix('proposal')->as('proposal.')->group(function () {
        // Pendaftaran
        Route::prefix('register')->as('register.')->group(function () {
            Route::controller(\App\Http\Controllers\Student\Proposal\RegisterController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/', 'update')->name('update');
                Route::get('/get/mentor', 'getMentor')->name('getMentor');
            });
        });
        
        // Jadwal
        Route::prefix('schedule')->as('schedule.')->group(function () {
            Route::controller(\App\Http\Controllers\Student\Proposal\ScheduleController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/download', 'download')->name('download');
            });
        });
    });

    // Tugas Akhir
    Route::prefix('final-project')->as('final_project.')->group(function () {
        // Pendaftaran
        Route::prefix('register')->as('register.')->group(function () {
            Route::controller(\App\Http\Controllers\Student\FinalProject\RegisterController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/', 'update')->name('update');
            });
        });
        
        // Jadwal
        Route::middleware('check.final_project')->group(function () {
            Route::prefix('schedule')->as('schedule.')->group(function () {
                Route::controller(\App\Http\Controllers\Student\FinalProject\ScheduleController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/download', 'download')->name('download');
                });
            });
        });
    });
});

// Dosen
Route::prefix('lecturer')->as('lecturer.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Lecturer\DashboardController::class, 'index'])->name('dashboard');

    // Proposal
    Route::prefix('proposal')->as('proposal.')->group(function () {
        // Jadwal
        Route::prefix('schedule')->as('schedule.')->group(function () {
            Route::controller(\App\Http\Controllers\Lecturer\Proposal\ScheduleController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get');  
            });
        });

        // Ujian
        Route::prefix('exam')->as('exam.')->group(function () {
            Route::controller(\App\Http\Controllers\Lecturer\Proposal\ExamController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get'); 
                Route::get('/get/rubric/{id}', 'getRubric')->name('getRubric');  
                Route::get('/get/assessment/{id}', 'getAssessment')->name('getAssessment');  
                Route::get('/download/pdf/{id}', 'generatePDF')->name('generatePDF');  
                Route::post('/', 'store')->name('store');
            });
        });
    });

    // Tugas Akhir
    Route::prefix('final-project')->as('final_project.')->group(function () {
        // Jadwal
        Route::prefix('schedule')->as('schedule.')->group(function () {
            Route::controller(\App\Http\Controllers\Lecturer\FinalProject\ScheduleController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get');  
            });
        });

        // Ujian
        Route::prefix('exam')->as('exam.')->group(function () {
            Route::controller(\App\Http\Controllers\Lecturer\FinalProject\ExamController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get'); 
                Route::get('/get/rubric/{id}', 'getRubric')->name('getRubric');  
                Route::get('/get/assessment/{id}', 'getAssessment')->name('getAssessment');  
                Route::get('/download/pdf/{id}', 'generatePDF')->name('generatePDF');  
                Route::post('/', 'store')->name('store');
            });
        });
    });
});
