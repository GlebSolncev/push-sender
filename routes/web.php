<?php

use App\Http\Controllers\PushController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
//    return view('welcome');
    return redirect()->to('admin');
});

Route::get('/checkkkkkk', fn() => view('welcome'));
Route::get('/test', [PushController::class, 'test']);
