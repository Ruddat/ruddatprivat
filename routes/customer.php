<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ImpersonateController;
use App\Http\Controllers\Backend\Customer\DashboardController;
use App\Http\Controllers\Backend\auth\CustomerOnboardingController;



    Route::get('/admin/impersonate/stop', [ImpersonateController::class, 'stop'])
        ->name('impersonate.stop');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Modul Dashboards
Route::get('dashboard/buchhaltung', [DashboardController::class, 'buchhaltung'])->name('dashboard.buchhaltung');
Route::get('dashboard/rechnungen', [DashboardController::class, 'rechnungen'])->name('dashboard.rechnungen');
Route::get('dashboard/nebenkosten', [DashboardController::class, 'nebenkosten'])->name('dashboard.nebenkosten');


// Onboarding Routen
        Route::get('onboarding', [CustomerOnboardingController::class, 'show'])->name('onboarding');
        Route::post('onboarding', [CustomerOnboardingController::class, 'store'])->name('onboarding.store');

// Onboarding Schritt 2 (Code prüfen)
Route::post('onboarding/verify', [CustomerOnboardingController::class, 'verifyCode'])
    ->name('onboarding.verify');

// profile
Route::get('profile', \App\Livewire\Backend\Customer\Profile::class)->name('profile');




//Route::get('/dashboard', function () {
//    return view('backend.customer.dashboard');
//})->name('dashboard');



// Nebenkosten Routen
Route::get('utility-costs/billing-headers', \App\Livewire\Backend\UtilityCosts\BillingHeaderForm::class)->name('utility_costs.billing_headers');
Route::get('utility-costs/billing-table', \App\Livewire\Backend\UtilityCosts\BillingTable::class)->name('utility_costs.billing_table');

Route::get('utility-costs/heating-costs', \App\Livewire\Backend\UtilityCosts\HeatingCostManagement::class)->name('utility_costs.heating_costs');

Route::get('utility-costs/refunds-or-payments', \App\Livewire\Backend\UtilityCosts\RefundsOrPaymentsComponent::class)->name('utility_costs.refunds_or_payments');

Route::get('utility-costs/billing-calculation', \App\Livewire\Backend\UtilityCosts\BillingCalculation::class)->name('utility_costs.billing_calculation');
Route::get('utility-costs/billing-generation', \App\Livewire\Backend\UtilityCosts\BillingGeneration::class)->name('utility_costs.billing_generation');

Route::get('utility-costs/rental-objects', \App\Livewire\Backend\UtilityCosts\RentalObjectTable::class)->name('utility_costs.rental_objects');
Route::get('utility-costs/tenants-payments', \App\Livewire\Backend\UtilityCosts\TenantPayments::class)->name('utility_costs.tenant_payments');
Route::get('utility-costs/tenants', \App\Livewire\Backend\UtilityCosts\TenantTable::class)->name('utility_costs.tenants');
Route::get('utility-costs/utility-cost-recording', \App\Livewire\Backend\UtilityCosts\UtilityCostRecording::class)->name('utility_costs.utility_cost_recording');
Route::get('utility-costs/utility-costs', \App\Livewire\Backend\UtilityCosts\UtilityCostTable::class)->name('utility_costs.utility_costs');
