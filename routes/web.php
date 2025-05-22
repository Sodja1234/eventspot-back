<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialiteController;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});


Route::get('/test', function () {
    return view('test-form');
});



Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
// routes/web.php
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');



    
require __DIR__.'/auth.php';