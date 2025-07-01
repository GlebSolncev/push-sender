<?php

use App\Http\Controllers\StatisticController;
use App\Http\Controllers\PushController;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Support\Facades\Route;

Route::prefix('push')->group(function () {
    Route::post('subscribe', [PushController::class, 'subscribe']);

    Route::post('statistic', [PushController::class, 'statistic']);
});

Route::prefix('statistics')->group(function() {
    Route::get('lead', [StatisticController::class, 'lead']);
    Route::get('sub', [StatisticController::class, 'sub'])
        ->withoutMiddleware([HandleCors::class]);

});