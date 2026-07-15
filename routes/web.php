<?php

use App\Http\Controllers\DriveShareController;
use App\Http\Controllers\Frontend\Appointment\AppointmentController;
use App\Http\Controllers\Frontend\CompleteIntake\CompleteIntakeController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LandingPageController;
use App\Http\Controllers\Frontend\PortfolioController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Frontend\SchedulingForm\SchedulingFormComponent;
use App\Livewire\Public\ProjectShareShow;
use App\Notifications\TelegramNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/schedule-appointment', [AppointmentController::class, 'index'])
    ->name('schedule.appointment');

Route::get('/intake-form', [CompleteIntakeController::class, 'index'])
    ->name('intake.form');

Route::get('/send-telegram', function () {
    $chatId = '6508551813';
    $message = 'Dies ist eine Testnachricht von deinem Telegram-Bot!';

    Notification::route('telegram', $chatId)
        ->notify(new TelegramNotification($message, $chatId));

    return 'Nachricht gesendet!';
});

Route::get('/schedule-meeting', SchedulingFormComponent::class);

Route::view('/impressum', 'frontend.home.sections.imprint')->name('impressum');
Route::view('/agb', 'frontend.home.sections.agb')->name('agb');
Route::view('/datenschutz', 'frontend.home.sections.privacy')->name('datenschutz');
Route::view('/portfolio', 'frontend.home.sections.portfolio')->name('portfolio');

Route::get('/portfolio/{portfolioItem:slug}', [PortfolioController::class, 'show'])
    ->name('portfolio.show');

Route::get('/share/project/{token}', ProjectShareShow::class)
    ->name('project-share.show');

Route::get('/share/drive/{token}', [DriveShareController::class, 'show'])
    ->name('drive.share.show');
Route::post('/share/drive/{token}/upload', [DriveShareController::class, 'upload'])
    ->name('drive.share.upload');
Route::get('/share/drive/{token}/download/{file}', [DriveShareController::class, 'download'])
    ->name('drive.share.download');
Route::get('/share/drive/{token}/stream/{file}', [DriveShareController::class, 'stream'])
    ->name('drive.share.stream');
Route::delete('/share/drive/{token}/delete/{file}', [DriveShareController::class, 'destroy'])
    ->name('drive.share.delete');
Route::get('/share/drive/{token}/folder/{folder}', [DriveShareController::class, 'folder'])
    ->name('drive.share.folder');
Route::post('/share/drive/{token}/chunk-upload', [\App\Http\Controllers\DriveChunkUploadController::class, 'store'])
    ->name('drive.share.chunk-upload');

Route::get('/lp/{slug}', [LandingPageController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-]+');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap');
