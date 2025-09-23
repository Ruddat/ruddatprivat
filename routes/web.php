<?php

use App\Models\FiscalYear;
use App\Exports\EntriesExport;
use App\Exports\EntriesRawExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use App\Notifications\TelegramNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PortfolioController;
use App\Http\Controllers\Frontend\LandingPageController;
use App\Livewire\Frontend\SchedulingForm\SchedulingFormComponent;
use App\Http\Controllers\Frontend\Appointment\AppointmentController;
use App\Http\Controllers\Frontend\CompleteIntake\CompleteIntakeController;

// Route::get('/', function () {
//    return view('welcome');
// });

// impersonate verlassen
//Route::get('/admin/impersonate/leave', function () {
//    $adminId = session('impersonate_admin_id');

//    if ($adminId) {
//        Auth::guard('admin')->loginUsingId($adminId);

        // Sessions bereinigen
//        session()->forget(['impersonate_admin_id', 'impersonated_customer_id']);/
 //   }

   // return redirect()->route('admin.dashboard');
//})->name('admin.impersonate.leave');



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

// Portfolio
Route::get('/portfolio', function () {
    return view('frontend.home.sections.portfolio');
})->name('portfolio');

Route::get('/portfolio/{portfolioItem:slug}', [PortfolioController::class, 'show'])
    ->name('portfolio.show');

// Admin Routes
Route::middleware(['auth'])->group(function () {});

// /Route::get('/admin/dashboard', Dashboard::class)->name('dashboard');

Route::get('/admin/portfolio-manager', \App\Livewire\Backend\PortfolioManager::class)->name('admin.portfolio.manager');

Route::get('/admin/portfolio-editor', \App\Livewire\Backend\PortfolioEditor::class)->name('admin.portfolio.editor');

// Buchahltungsrouten
Route::get('/admin/bookkeeping', \App\Livewire\Backend\Bookkeeping\EntryForm::class)->name('admin.bookkeeping.dashboard');

Route::get('/admin/bookkeeping/entries', \App\Livewire\Backend\Bookkeeping\EntryList::class)->name('admin.bookkeeping.entries');

Route::get('/admin/bookkeeping/report-profit-loss', \App\Livewire\Backend\Bookkeeping\ReportProfitLoss::class)->name('admin.bookkeeping.report_profit_loss');

Route::get('/admin/bookkeeping/report-vat', \App\Livewire\Backend\Bookkeeping\ReportVat::class)->name('admin.bookkeeping.report_vat');

Route::get('/admin/bookkeeping/fiscal-years', \App\Livewire\Backend\Bookkeeping\FiscalYearForm::class)->name('admin.bookkeeping.fiscal_years');

Route::get('/admin/bookkeeping/tenants', \App\Livewire\Backend\Bookkeeping\TenantManager::class)->name('admin.bookkeeping.tenants');

Route::get('/admin/bookkeeping/accounts', \App\Livewire\Backend\Bookkeeping\AccountManager::class)->name('admin.bookkeeping.accounts');

Route::get('/admin/bookkeeping/opening-balance', \App\Livewire\Backend\Bookkeeping\OpeningBalanceForm::class)->name('admin.bookkeeping.opening_balance');

Route::get('/export/fancy', function () {
    $tenantId = session('current_tenant_id', 1);
    $fiscalYear = FiscalYear::current($tenantId);

    return Excel::download(new EntriesExport($tenantId, $fiscalYear->id), 'buchungen.xlsx');
})->name('admin.bookkeeping.entries.export.fancy');

Route::get('/export/raw', function () {
    $tenantId = session('current_tenant_id', 1);
    $fiscalYear = FiscalYear::current($tenantId);

    return Excel::download(new EntriesRawExport($tenantId, $fiscalYear->id), 'buchungen_raw.xlsx');
})->name('admin.bookkeeping.entries.export.raw');

// End Admin Routes

Route::get('/lp/{slug}', [LandingPageController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-]+');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Nebenkosten konstrukt

//   Route::get('/admin/utility-costs/dashboard', \App\Livewire\Backend\UtilityCosts\Dashboard::class)->name('admin.utility_costs.dashboard');

// Route::get('/admin/utility-costs/billing-calculation', \App\Livewire\Backend\UtilityCosts\BillingCalculation::class)->name('admin.utility_costs.billing_calculation');

// Route::get('/admin/utility-costs/billing-generation', \App\Livewire\Backend\UtilityCosts\BillingGeneration::class)->name('admin.utility_costs.billing_generation');

// Route::get('/admin/utility-costs/billing-headers', \App\Livewire\Backend\UtilityCosts\BillingHeaderForm::class)->name('admin.utility_costs.billing_headers');

// Route::get('/admin/utility-costs/billing-table', \App\Livewire\Backend\UtilityCosts\BillingTable::class)->name('admin.utility_costs.billing_table');

// Route::get('/admin/utility-costs/heating-costs', \App\Livewire\Backend\UtilityCosts\HeatingCostManagement::class)->name('admin.utility_costs.heating_costs');

// Route::get('/admin/utility-costs/refunds-or-payments', \App\Livewire\Backend\UtilityCosts\RefundsOrPaymentsComponent::class)->name('admin.utility_costs.refunds_or_payments');

// Route::get('/admin/utility-costs/rental-objects', \App\Livewire\Backend\UtilityCosts\RentalObjectTable::class)->name('admin.utility_costs.rental_objects');

// Route::get('/admin/utility-costs/tenants-payments', \App\Livewire\Backend\UtilityCosts\TenantPayments::class)->name('admin.utility_costs.tenant_payments');

// Route::get('/admin/utility-costs/tenants', \App\Livewire\Backend\UtilityCosts\TenantTable::class)->name('admin.utility_costs.tenants');

// Route::get('/admin/utility-costs/utility-cost-recording', \App\Livewire\Backend\UtilityCosts\UtilityCostRecording::class)->name('admin.utility_costs.utility_cost_recording');

// Route::get('/admin/utility-costs/utility-costs', \App\Livewire\Backend\UtilityCosts\UtilityCostTable::class)->name('admin.utility_costs.utility_costs');

//        Route::get('/admin/utility-costs/rental-objects', \App\Livewire\Backend\UtilityCosts\RentalObjectManager::class)->name('admin.utility_costs.rental_objects');

//      Route::get('/admin/utility-costs/tenants', \App\Livewire\Backend\UtilityCosts\TenantManager::class)->name('admin.utility_costs.tenants');

//    Route::get('/admin/utility-costs/utility-costs', \App\Livewire\Backend\UtilityCosts\UtilityCostManager::class)->name('admin.utility_costs.utility_costs');

//  Route::get('/admin/utility-costs/tenant-payments', \App\Livewire\Backend\UtilityCosts\TenantPaymentManager::class)->name('admin.utility_costs.tenant_payments');

// Route::get('/admin/utility-costs/billing-headers', \App\Livewire\Backend\UtilityCosts\BillingHeaderManager::class)->name('admin.utility_costs.billing_headers');

//       Route::get('/admin/utility-costs/billing-records', \App\Livewire\Backend\UtilityCosts\BillingRecordManager::class)->name('admin.utility_costs.billing_records');

//        Route::get('/admin/utility-costs/generate-billing', \App\Livewire\Backend\UtilityCosts\GenerateBillingForm::class)->name('admin.utility_costs.generate_billing');
