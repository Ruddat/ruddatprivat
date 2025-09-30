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


// Rechnungen Routen
//Route::get('e-invoice/invoice-creators', \App\Livewire\Backend\EInvoice\InvoiceCreatorsManager::class)->name('e_invoice.invoice_creators');

Route::get('e-invoice/customer-manager', \App\Livewire\Backend\EInvoice\CustomerManager::class)->name('e_invoice.customer_manager');


    Route::get('new_invoice/invoice-manager', \App\Livewire\Backend\EInvoice\InvoiceManager::class)->name('new_invoice.invoice_manager');

    Route::get('new_invoice/pdf-manager', \App\Livewire\Backend\EInvoice\InvoicePdfManager::class)->name('new_invoice.pdf_manager');


Route::get('e-invoice/invoice-headers', \App\Livewire\Backend\EInvoice\InvoiceHeaderManager::class)->name('e_invoice.invoice_headers');
Route::get('e-invoice/invoice-recipients', \App\Livewire\Backend\EInvoice\InvoiceRecipientsManager::class)->name('e_invoice.invoice_recipients');



Route::get('e-invoice/manage-invoice-recipients', \App\Livewire\Backend\EInvoice\ManageInvoiceRecipients::class)->name('e_invoice.manage_invoice_recipients');

// Quittungen Routen
Route::get('receipts/receipt-manager', \App\Livewire\Backend\Receipt\ReceiptManager::class)->name('receipts.receipt_manager');


// Buchahltungsrouten
Route::get('/bookkeeping', \App\Livewire\Backend\Bookkeeping\EntryForm::class)->name('bookkeeping.dashboard');

Route::get('/bookkeeping/entries', \App\Livewire\Backend\Bookkeeping\EntryList::class)->name('bookkeeping.entries');

Route::get('/bookkeeping/report-profit-loss', \App\Livewire\Backend\Bookkeeping\ReportProfitLoss::class)->name('bookkeeping.report_profit_loss');

Route::get('/bookkeeping/report-vat', \App\Livewire\Backend\Bookkeeping\ReportVat::class)->name('bookkeeping.report_vat');

Route::get('/bookkeeping/fiscal-years', \App\Livewire\Backend\Bookkeeping\FiscalYearForm::class)->name('bookkeeping.fiscal_years');

Route::get('/bookkeeping/tenants', \App\Livewire\Backend\Bookkeeping\TenantManager::class)->name('bookkeeping.tenants');

Route::get('/bookkeeping/accounts', \App\Livewire\Backend\Bookkeeping\AccountManager::class)->name('bookkeeping.accounts');

Route::get('/bookkeeping/opening-balance', \App\Livewire\Backend\Bookkeeping\OpeningBalanceForm::class)->name('bookkeeping.opening_balance');

Route::get('/bookkeeping/invoice-upload', \App\Livewire\Backend\Bookkeeping\InvoiceUploadForm::class)->name('bookkeeping.invoice_uploads');


Route::get('/bookkeeping/report-bwa', \App\Livewire\Backend\Bookkeeping\ReportBwa::class)->name('bookkeeping.report_bwa');

Route::get('/bookkeeping/tank-receipt-upload', \App\Livewire\Backend\Bookkeeping\TankReceiptUpload::class)->name('bookkeeping.tank_receipt_upload');

Route::get('/bookkeeping/report-balance-sheet', \App\Livewire\Backend\Bookkeeping\ReportBalanceSheet::class)->name('bookkeeping.report_balance_sheet');

Route::get('/bookkeeping/import-entries', \App\Livewire\Backend\Bookkeeping\ImportEntries::class)->name('bookkeeping.import_entries');



// Feedback
Route::get('/feedback', \App\Livewire\Backend\Customer\FeedbackForm::class)->name('feedback');
Route::get('/feedback-board', \App\Livewire\Backend\Customer\FeedbackBoard::class)->name('feedback.board');
// Ende Feedback