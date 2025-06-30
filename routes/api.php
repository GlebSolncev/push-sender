<?php

use App\Http\Controllers\LeadController;
use App\Http\Controllers\PushController;
use Illuminate\Support\Facades\Route;

Route::prefix('push')->group(function () {
    Route::post('subscribe', [PushController::class, 'subscribe']);

    Route::post('statistic', [PushController::class, 'statistic']);
});

Route::prefix('statistics')->group(function() {
    Route::get('lead', [LeadController::class, 'lead']);


});