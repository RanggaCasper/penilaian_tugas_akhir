<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(\App\Http\Controllers\Api\Proposal\ProposalController::class)->group(function () {
    Route::get('/proposal', 'index')->name('index');
});