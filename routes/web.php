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
Route::prefix('super')->as('super.')->middleware('auth', 'checkRole:Super')->group(function () {
    Route::get('/', [\App\Http\Controllers\Super\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'store'])->name('profile.store');
    
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
            Route::get('/get', 'get')->name('get');
            Route::post('/get-data', 'getData')->name('getData');
        });
    });

    // Admin
    Route::prefix('admin')->as('admin.')->group(function () {
        Route::controller(\App\Http\Controllers\Super\Admin\AdminController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/get', 'get')->name('get');
            Route::post('/get-data', 'getData')->name('getData');
            Route::get('/get/{id}', 'getById')->name('getById');
            Route::put('{id}', 'update')->name('update');
            Route::delete('{id}', 'destroy')->name('destroy');
        });
    });

     // Program Study
     Route::prefix('program_study')->as('program_study.')->group(function () {
        Route::controller(\App\Http\Controllers\Super\ProgramStudy\ProgramStudyController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get', 'get')->name('get');
            Route::post('/get-data', 'getData')->name('getData');
        });
    });
});

// Admin
Route::prefix('admin')->as('admin.')->middleware('auth', 'checkRole:Admin')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'store'])->name('profile.store');

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
    Route::prefix('thesis')->as('thesis.')->group(function () {
        // Pendaftaran
        Route::prefix('register')->as('register.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Thesis\RegisterController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::delete('{id}', 'destroy')->name('destroy');
            });
        });

        // Jadwal
        Route::prefix('schedule')->as('schedule.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Thesis\ScheduleController::class)->group(function () {
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

    // Hasil
    Route::prefix('result')->as('result.')->group(function () {       
        // Nilai Mahasiswa
        Route::prefix('score')->as('score.')->group(function () {       
            Route::controller(\App\Http\Controllers\Admin\Result\ScoreController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::get('/download/{type}/{id}/{download}', 'download')->name('download');            
            });
        });

        // Nilai Tugas Akhir
        Route::prefix('final_score')->as('final_score.')->group(function () {       
            Route::controller(\App\Http\Controllers\Admin\Result\FinalScoreController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get');
                Route::get('/get/{id}', 'getById')->name('getById');
                Route::put('{id}', 'update')->name('update');
                Route::get('/download/{id}/{download}', 'download')->name('download');            
            });
        });
    });
});

// Mahasiswa
Route::prefix('student')->as('student.')->middleware('auth', 'checkRole:Student')->group(function () {
    Route::get('/', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'store'])->name('profile.store');

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
    Route::prefix('thesis')->as('thesis.')->group(function () {
        // Pendaftaran
        Route::prefix('register')->as('register.')->group(function () {
            Route::controller(\App\Http\Controllers\Student\Thesis\RegisterController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/', 'update')->name('update');
            });
        });
        
        // Jadwal
        Route::middleware('check.thesis')->group(function () {
            Route::prefix('schedule')->as('schedule.')->group(function () {
                Route::controller(\App\Http\Controllers\Student\Thesis\ScheduleController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/download', 'download')->name('download');
                });
            });
        });
    });

    Route::prefix('result')->as('result.')->group(function () {
        // Hasil
        Route::controller(\App\Http\Controllers\Student\Result\ResultController::class)->group(function () {
            Route::get('/', 'index')->name('index');
        });

        // Dokumen Revisi
        Route::prefix('revisi')->as('revisi.')->group(function () {
            Route::controller(\App\Http\Controllers\Student\Result\RevisiController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/download/{type}/{id}', 'generatePdf')->name('generatePdf');
            });
        });

        // Dokumen Pertanyaan
        Route::prefix('question')->as('question.')->group(function () {
            Route::controller(\App\Http\Controllers\Student\Result\QuestionController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/download/{type}/{id}', 'generatePdf')->name('generatePdf');
            });
        });

        // Dokumen
        Route::prefix('document')->as('document.')->group(function () {
            Route::controller(\App\Http\Controllers\Student\Result\DocumentController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/download/{type}/{id}', 'generatePdf')->name('generatePdf');
            });
        });

    });
});

// Dosen
Route::prefix('lecturer')->as('lecturer.')->middleware('auth', 'checkRole:Lecturer')->group(function () {
    Route::get('/', [App\Http\Controllers\Lecturer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'store'])->name('profile.store');

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
    Route::prefix('thesis')->as('thesis.')->group(function () {
        // Jadwal
        Route::prefix('schedule')->as('schedule.')->group(function () {
            Route::controller(\App\Http\Controllers\Lecturer\Thesis\ScheduleController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get');  
            });
        });

        // Ujian
        Route::prefix('exam')->as('exam.')->group(function () {
            Route::controller(\App\Http\Controllers\Lecturer\Thesis\ExamController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get', 'get')->name('get'); 
                Route::get('/get/rubric/{id}', 'getRubric')->name('getRubric');  
                Route::get('/get/assessment/{id}', 'getAssessment')->name('getAssessment');  
                Route::get('/download/pdf/{id}', 'generatePDF')->name('generatePDF');  
                Route::post('/', 'store')->name('store');
            });
        });
    });

    // Pembimbing
    Route::prefix('mentor')->as('mentor.')->group(function () {
        Route::controller(\App\Http\Controllers\Lecturer\Mentor\MentorController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get', 'get')->name('get');  
            Route::post('/', 'store')->name('store');  
            Route::get('/get/rubric/{id}', 'getRubric')->name('getRubric');  
            Route::get('/download/pdf/{id}', 'generatePDF')->name('generatePDF'); 
        });
    });
});

// Special
Route::prefix('special')->as('special.')->middleware('auth', 'checkRole:Special')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'store'])->name('profile.store');

    Route::get('/', [App\Http\Controllers\Special\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api', [App\Http\Controllers\Special\Api\ApiController::class, 'index'])->name('api.setting');
    Route::post('/api/regenerate', [App\Http\Controllers\Special\Api\ApiController::class, 'regenerate'])->name('api.regenerate');
    Route::put('/api', [App\Http\Controllers\Special\Api\ApiController::class, 'update'])->name('api.update');
    Route::get('/api/document', [App\Http\Controllers\Special\Api\DocumentController::class, 'index'])->name('api.document');
});
