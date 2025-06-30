<?php

use App\Http\Controllers\PushController;
use Illuminate\Support\Facades\Route;

Route::prefix('push')->group(function () {
    Route::put('subscribe', [PushController::class, 'subscribe']);

    Route::post('statistic', [PushController::class, 'statistic']);
});