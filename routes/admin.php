<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ImpersonateController;

// ✅ alle Routen hier sind geschützt durch auth:admin
Route::get('/dashboard', fn () => view('backend.admin.dashboard'))->name('dashboard');

Route::get('/admin/customers', \App\Livewire\Backend\Admin\Customer\CustomersTable::class)->name('admin.customers.index');




    Route::get('/admin/impersonate/{customer}', [ImpersonateController::class, 'start'])
        ->name('impersonate.start');

    Route::get('/admin/impersonate/stop', [ImpersonateController::class, 'stop'])
        ->name('impersonate.stop');

// System
        Route::get('settings', \App\Livewire\Backend\Admin\System\SettingsForm::class)
    ->name('settings');