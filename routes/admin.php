<?php

use App\Http\Controllers\Backend\ImpersonateController;
use App\Livewire\Admin\ProjectHub\BoardIndex;
use App\Livewire\Admin\ProjectHub\BoardShow;
use App\Livewire\Backend\PortfolioEditor;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', fn () => view('backend.admin.dashboard'))
    ->name('dashboard');

Route::get('/drive', \