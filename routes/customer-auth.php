<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Auth\CustomerAuthController;
use App\Http\Controllers\Backend\Auth\CustomerRegisterController;



Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

    Route::get('register', [CustomerRegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [CustomerRegisterController::class, 'register'])->name('register.submit');