<?php

use App\Http\Controllers\Backend\ImpersonateController;
use App\Livewire\Admin\ProjectHub\BoardIndex;
use App\Livewire\Admin\ProjectHub\BoardShow;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', fn () => view('backend.admin.dashboard'))
    ->name('dashboard');

Route::get('/drive', \App\Livewire\Backend\Drive\FileManager::class)
    ->name('drive');

Route::get('/drive/download/{file}', \App\Http\Controllers\DriveDownloadController::class)
    ->name('drive.download');

Route::get('/drive/stream/{file}', \App\Http\Controllers\DriveStreamController::class)
    ->name('drive.stream');

Route::get('/projecthub', BoardIndex::class)
    ->name('projecthub.index');

Route::get('/projecthub/{board}', BoardShow::class)
    ->name('projecthub.show');

Route::get('/customers', \App\Livewire\Backend\Admin\Customer\CustomersTable::class)
    ->name('customers.index');

Route::post('/customers/{customer}/impersonate', [ImpersonateController::class, 'start'])
    ->name('impersonate.start');

Route::post('/impersonate/stop', [ImpersonateController::class, 'stop'])
    ->name('impersonate.stop');

Route::get('/settings', \App\Livewire\Backend\Admin\System\SettingsForm::class)
    ->name('settings');

Route::get('/customers/feedback', \App\Livewire\Backend\Admin\Customer\FeedbackManager::class)
    ->name('customers.feedback');
