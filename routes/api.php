<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware('checkSignature')->controller(\App\Http\Controllers\Api\Proposal\ProposalController::class)->group(function () {
    Route::post('/proposal', 'get')->name('api.proposal.get');
});