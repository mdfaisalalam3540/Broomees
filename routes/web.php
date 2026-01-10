<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;


Route::get('/', function () {
    return view('home');
});

Route::post('/register', [UsersController::class, 'register']);
