<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware('checkSignature', 'throttle:10,1')->controller(\App\Http\Controllers\Api\Proposal\ProposalController::class)->group(function () {
    Route::post('/proposal', 'get')->name('api.proposal.get');
});