<?php

use App\Http\Controllers\PushController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', [PushController::class, 'test']);
