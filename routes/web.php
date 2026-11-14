<?php

use App\Http\Controllers\DriveShareController;
use App\Http\Controllers\Frontend\Appointment\AppointmentController;
use App\Http\Controllers\Frontend\CompleteIntake\CompleteIntakeController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LandingPageController;
use App\Http\Controllers\Frontend\ReferenceController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Frontend\SchedulingForm\SchedulingFormComponent;
use App\Livewire\Public\ProjectShareShow;
use App\Support\Site\SiteContent;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/schedule-appointment', [AppointmentController::class, 'index'])
    ->name('schedule.appointment');

Route::get('/intake-form', [CompleteIntakeController::class, 'index'])
    ->name('intake.form');


Route::get('/schedule-meeting', SchedulingFormComponent::class);

Route::view('/impressum', 'site.legal.imprint')->name('impressum');
Route::view('/agb', 'site.legal.terms')->name('agb');
Route::view('/datenschutz', 'site.legal.privacy')->name('datenschutz');
// RuddatTech – neue Seitenstruktur
Route::get('/leistungen', [ServiceController::class, 'index'])->name('leistungen.index');
Route::get('/leistungen/{slug}', [ServiceController::class, 'show'])
    ->whereIn('slug', array_keys(SiteContent::services()))
    ->name('leistungen.show');

Route::get('/referenzen', [ReferenceController::class, 'index'])->name('referenzen.index');
Route::get('/referenzen/{reference:slug}', [ReferenceController::class, 'show'])->name('referenzen.show');

Route::view('/ueber-ruddattech', 'site.about')->name('about');
Route::view('/kontakt', 'site.contact')->name('kontakt');

// Alte Portfolio-URLs: alle bisherigen Einträge sind unveröffentlicht → 301 auf die Referenzübersicht
Route::permanentRedirect('/portfolio', '/referenzen')->name('portfolio');
Route::permanentRedirect('/portfolio/{slug}', '/referenzen')->name('portfolio.show');

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

// Ersetzt die statische public/robots.txt (Sitemap-URL je Umgebung)
Route::get('/robots.txt', \App\Http\Controllers\RobotsController::class)->name('robots');
