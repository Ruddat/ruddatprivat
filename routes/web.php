<?php

use Illuminate\Support\Facades\Route;
use App\Notifications\TelegramNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Frontend\HomeController;
use App\Livewire\Frontend\SchedulingForm\SchedulingFormComponent;
use App\Http\Controllers\Frontend\Appointment\AppointmentController;
use App\Http\Controllers\Frontend\CompleteIntake\CompleteIntakeController;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/schedule-appointment', [AppointmentController::class, 'index'])->name('schedule.appointment');

Route::get('/intake-form', [CompleteIntakeController::class, 'index'])->name('intake.form');

Route::get('/send-telegram', function () {
    $chatId = '6508551813'; // Chat-ID des Benutzers
    $message = 'Dies ist eine Testnachricht von deinem Telegram-Bot!';

    Notification::route('telegram', $chatId)->notify(new TelegramNotification($message, $chatId));

    return 'Nachricht gesendet!';
});

Route::get('/schedule-meeting', SchedulingFormComponent::class);


// Impressum
Route::get('/impressum', function () {
    return view('frontend.home.sections.imprint');
})->name('impressum');

// AGB
Route::get('/agb', function () {
    return view('frontend.home.sections.agb');
})->name('agb');

// Datenschutz
Route::get('/datenschutz', function () {
    return view('frontend.home.sections.privacy');
})->name('datenschutz');
