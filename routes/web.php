<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->as('auth.')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::get('/js/config', function () {
    return response()->json([
        'data' => [
            'BASE_URL' => env('APP_URL')
        ],
    ]);
})->name('scripts.config');