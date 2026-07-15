<?php

use App\Exports\EntriesExport;
use App\Exports\EntriesRawExport;
use App\Http\Controllers\Backend\Auth\CustomerOnboardingController;
use App\Http\Controllers\Backend\Customer\DashboardController;
use App\Http\Controllers\Backend\ImpersonateController;
use App\Models\FiscalYear;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/dashboard/buchhaltung', [DashboardController::class, 'buchhaltung'])
    ->name('dashboard.buchhaltung');
Route::get('/dashboard/rechnungen', [DashboardController::class, 'rechnungen'])
    ->name('dashboard.rechnungen');
Route::get('/dashboard/nebenkosten', [DashboardController::class, 'nebenkosten'])
    ->name('dashboard.nebenkosten');

Route::get('/onboarding', [CustomerOnboardingController::class, 'show'])
    ->name('onboarding');
Route::post('/onboarding', [CustomerOnboardingController::class, 'store'])
    ->name('onboarding.store');
Route::post('/onboarding/verify', [CustomerOnboardingController::class, 'verifyCode'])
    ->name('onboarding.verify');

Route::get('/profile', \App\Livewire\Backend\Customer\Profile::class)
    ->name('profile');

Route::get('/impersonate/stop', [ImpersonateController::class, 'stop'])
    ->name('impersonate.stop');

// Nebenkosten
Route::prefix('utility-costs')->name('utility_costs.')->group(function (): void {
    Route::get('/billing-headers', \App\Livewire\Backend\UtilityCosts\BillingHeaderForm::class)->name('billing_headers');
    Route::get('/billing-table', \App\Livewire\Backend\UtilityCosts\BillingTable::class)->name('billing_table');
    Route::get('/heating-costs', \App\Livewire\Backend\UtilityCosts\HeatingCostManagement::class)->name('heating_costs');
    Route::get('/refunds-or-payments', \App\Livewire\Backend\UtilityCosts\RefundsOrPaymentsComponent::class)->name('refunds_or_payments');
    Route::get('/billing-calculation', \App\Livewire\Backend\UtilityCosts\BillingCalculation::class)->name('billing_calculation');
    Route::get('/billing-generation', \App\Livewire\Backend\UtilityCosts\BillingGeneration::class)->name('billing_generation');
    Route::get('/rental-objects', \App\Livewire\Backend\UtilityCosts\RentalObjectTable::class)->name('rental_objects');
    Route::get('/tenants-payments', \App\Livewire\Backend\UtilityCosts\TenantPayments::class)->name('tenants_payments');
    Route::get('/tenants', \App\Livewire\Backend\UtilityCosts\TenantTable::class)->name('tenants');
    Route::get('/utility-cost-recording', \App\Livewire\Backend\UtilityCosts\UtilityCostRecording::class)->name('utility_cost_recording');
    Route::get('/utility-costs', \App\Livewire\Backend\UtilityCosts\UtilityCostTable::class)->name('utility_costs');
});

// E-Rechnung
Route::prefix('e-invoice')->name('e_invoice.')->group(function (): void {
    Route::get('/customer-manager', \App\Livewire\Backend\EInvoice\CustomerManager::class)->name('customer_manager');
    Route::get('/invoice-manager', \App\Livewire\Backend\EInvoice\InvoiceManager::class)->name('invoice_manager');
    Route::get('/pdf-manager', \App\Livewire\Backend\EInvoice\InvoicePdfManager::class)->name('pdf_manager');
    Route::get('/invoice-headers', \App\Livewire\Backend\EInvoice\InvoiceHeaderManager::class)->name('invoice_headers');
    Route::get('/invoice-recipients', \App\Livewire\Backend\EInvoice\InvoiceRecipientsManager::class)->name('invoice_recipients');
    Route::get('/manage-invoice-recipients', \App\Livewire\Backend\EInvoice\ManageInvoiceRecipients::class)->name('manage_invoice_recipients');
});

// Quittungen
Route::get('/receipts', \App\Livewire\Backend\Receipt\ReceiptManager::class)
    ->name('receipts.index');

// Buchhaltung
Route::prefix('bookkeeping')->name('bookkeeping.')->group(function (): void {
    Route::get('/', \App\Livewire\Backend\Bookkeeping\EntryForm::class)->name('dashboard');
    Route::get('/entries', \App\Livewire\Backend\Bookkeeping\EntryList::class)->name('entries');
    Route::get('/report-profit-loss', \App\Livewire\Backend\Bookkeeping\ReportProfitLoss::class)->name('report_profit_loss');
    Route::get('/report-vat', \App\Livewire\Backend\Bookkeeping\ReportVat::class)->name('report_vat');
    Route::get('/fiscal-years', \App\Livewire\Backend\Bookkeeping\FiscalYearForm::class)->name('fiscal_years');
    Route::get('/tenants', \App\Livewire\Backend\Bookkeeping\TenantManager::class)->name('tenants');
    Route::get('/accounts', \App\Livewire\Backend\Bookkeeping\AccountManager::class)->name('accounts');
    Route::get('/opening-balance', \App\Livewire\Backend\Bookkeeping\OpeningBalanceForm::class)->name('opening_balance');
    Route::get('/invoice-upload', \App\Livewire\Backend\Bookkeeping\InvoiceUploadForm::class)->name('invoice_uploads');
    Route::get('/report-bwa', \App\Livewire\Backend\Bookkeeping\ReportBwa::class)->name('report_bwa');
    Route::get('/tank-receipt-upload', \App\Livewire\Backend\Bookkeeping\TankReceiptUpload::class)->name('tank_receipt_upload');
    Route::get('/report-balance-sheet', \App\Livewire\Backend\Bookkeeping\ReportBalanceSheet::class)->name('report_balance_sheet');
    Route::get('/import-entries', \App\Livewire\Backend\Bookkeeping\ImportEntries::class)->name('import_entries');

    Route::get('/export/fancy', function () {
        $tenantId = session('current_tenant_id', 1);
        $fiscalYear = FiscalYear::current($tenantId);

        return Excel::download(new EntriesExport($tenantId, $fiscalYear->id), 'buchungen.xlsx');
    })->name('entries.export.fancy');

    Route::get('/export/raw', function () {
        $tenantId = session('current_tenant_id', 1);
        $fiscalYear = FiscalYear::current($tenantId);

        return Excel::download(new EntriesRawExport($tenantId, $fiscalYear->id), 'buchungen_raw.xlsx');
    })->name('entries.export.raw');
});

Route::get('/feedback', \App\Livewire\Backend\Customer\FeedbackForm::class)
    ->name('feedback');
Route::get('/feedback-board', \App\Livewire\Backend\Customer\FeedbackBoard::class)
    ->name('feedback.board');
