<?php

use App\Http\Controllers\Backend\Auth\AdminAuthController;
use Illuminate\Support\Facades\Route;

// Login & Logout (❌ kein auth:admin!)
Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
