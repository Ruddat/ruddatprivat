<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Entry;
use App\Models\Tenant;
use App\Models\Account;
use Livewire\Component;
use App\Models\BkReceipt;
use App\Models\FiscalYear;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\BkBookingTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EntryForm extends Component
{
    use WithFileUploads;

    public $tenantId;
    public $currentTenant;
    public $currentFiscalYear;

    public $booking_date;
    public $debit_account_id;
    public $credit_account_id;
    public $net_amount;
    public $description;
    public $vat_rate = 19;
    public $with_vat = false;
    public $input_mode = 'netto';


public $fiscalYearStart;
public $fiscalYearEnd;

    // Beleg
    public $receipt_file;
    public $receipt_type;
    public $receipt_currency = 'EUR';

    // Buchungsvorlagen
    public $templates = [];
    public $selectedTemplate = null;
    public $showTemplates = false;

public $showTemplatePrompt = false;

    public $availableTenants;


    protected $rules = [
        'booking_date' => 'required|date',
        'debit_account_id' => 'required|integer',
        'credit_account_id' => 'required|integer|different:debit_account_id',
        'net_amount' => 'required|numeric|min:0.01',
        'vat_rate' => 'nullable|numeric|min:0',
        'description' => 'nullable|string|max:255',
        'receipt_file' => 'nullable|file|max:5120',
    ];

public function mount()
{
    // Gleiche Logik wie in BWA und FiscalYearForm verwenden
    $this->availableTenants = Tenant::where('customer_id', Auth::guard('customer')->id())
        ->orderBy('name')
        ->get();

    // Aktuellen Mandanten finden oder ersten nehmen
    $currentTenant = $this->availableTenants->where('is_current', true)->first();
    $firstTenant = $this->availableTenants->first();
    
    $this->tenantId = $currentTenant ? $currentTenant->id : ($firstTenant ? $firstTenant->id : null);

    $this->loadCurrentTenant();
    $this->loadCurrentFiscalYear();
    $this->loadTemplates();
    $this->setDefaultBookingDate();
}

public function loadCurrentTenant()
{
    $this->currentTenant = $this->availableTenants->firstWhere('id', $this->tenantId);
}

public function loadCurrentFiscalYear()
{
    try {
        $this->currentFiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
            ->where('is_current', true)
            ->first();

        // Carbon Objects für die Blade-View vorbereiten
        if ($this->currentFiscalYear) {
            $this->fiscalYearStart = \Carbon\Carbon::parse($this->currentFiscalYear->start_date);
            $this->fiscalYearEnd = \Carbon\Carbon::parse($this->currentFiscalYear->end_date);
        } else {
            $this->fiscalYearStart = null;
            $this->fiscalYearEnd = null;

            // Logging für Debugging
            Log::warning("Kein aktuelles Geschäftsjahr gefunden für Tenant: " . $this->tenantId);
        }
    } catch (\Exception $e) {
        Log::error('Fehler in loadCurrentFiscalYear: ' . $e->getMessage());
        $this->currentFiscalYear = null;
        $this->fiscalYearStart = null;
        $this->fiscalYearEnd = null;
    }
}

public function updatedTenantId($value)
{
    try {
        $this->tenantId = $value;
        $this->loadCurrentTenant();
        $this->loadCurrentFiscalYear();
        $this->setDefaultBookingDate();
        $this->loadTemplates();
        $this->resetForm();
    } catch (\Exception $e) {
        Log::error('Fehler beim Wechseln des Mandanten: ' . $e->getMessage());
        session()->flash('error', 'Fehler beim Wechseln des Mandanten: ' . $e->getMessage());
    }
}

public function setDefaultBookingDate()
{
    if ($this->currentFiscalYear) {
        try {
            // Sicherheitsprüfung und Konvertierung
            $startDate = \Carbon\Carbon::parse($this->currentFiscalYear->start_date);
            $endDate = \Carbon\Carbon::parse($this->currentFiscalYear->end_date);

            $today = now();
            if ($today->between($startDate, $endDate)) {
                $this->booking_date = $today->format('Y-m-d');
            } else {
                $this->booking_date = $startDate->format('Y-m-d');
            }
        } catch (\Exception $e) {
            // Fallback bei Fehler
            $this->booking_date = now()->format('Y-m-d');
            Log::error('Fehler in setDefaultBookingDate: ' . $e->getMessage());
        }
    } else {
        // Fallback: heutiges Datum
        $this->booking_date = now()->format('Y-m-d');
    }
}

    public function loadTemplates()
    {
        $this->templates = BkBookingTemplate::where('tenant_id', $this->tenantId)
            ->orWhere('is_global', true)
            ->orderBy('name')
            ->get();
    }



    public function applyTemplate($templateId)
    {
        $template = BkBookingTemplate::find($templateId);

        if ($template) {
            $this->debit_account_id = $template->debit_account_id;
            $this->credit_account_id = $template->credit_account_id;
            $this->vat_rate = $template->vat_rate;
            $this->with_vat = $template->with_vat;
            $this->description = $template->description;
            $this->receipt_type = $template->receipt_type;

            session()->flash('template_message', "Vorlage '{$template->name}' wurde geladen.");
        }
    }

    public function saveTemplate()
    {
        $validated = $this->validate([
            'debit_account_id' => 'required|integer',
            'credit_account_id' => 'required|integer|different:debit_account_id',
            'description' => 'required|string|max:255',
        ]);

        BkBookingTemplate::create([
            'tenant_id' => $this->tenantId,
            'name' => $this->description . ' Vorlage',
            'debit_account_id' => $this->debit_account_id,
            'credit_account_id' => $this->credit_account_id,
            'vat_rate' => $this->vat_rate,
            'with_vat' => $this->with_vat,
            'description' => $this->description,
            'receipt_type' => $this->receipt_type,
            'is_global' => false,
        ]);

        $this->loadTemplates();
        session()->flash('success', 'Buchungsvorlage wurde gespeichert!');
    }

public function save()
{
    $this->validate();

    // Geschäftsjahr für das Buchungsdatum ermitteln
    $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
        ->whereDate('start_date', '<=', $this->booking_date)
        ->whereDate('end_date', '>=', $this->booking_date)
        ->first();

    if (! $fiscalYear) {
        session()->flash('error', "Für das gewählte Datum ({$this->booking_date}) existiert kein Buchungsjahr!");
        return;
    }

    if ($fiscalYear->closed) {
        session()->flash('error', "Das Buchungsjahr {$fiscalYear->year} ist geschlossen – keine Buchung möglich.");
        return;
    }

    [$net, $vat, $gross] = $this->computeAmounts();

    // DEBUG: Prüfen ob Daten ankommen
    Log::info('Buchung wird gespeichert', [
        'tenant_id' => $this->tenantId,
        'booking_date' => $this->booking_date,
        'fiscal_year_id' => $fiscalYear->id,
        'debit_account_id' => $this->debit_account_id,
        'credit_account_id' => $this->credit_account_id,
        'net_amount' => $this->net_amount,
        'description' => $this->description,
    ]);

    // ggf. Beleg speichern
    $receiptId = null;
    if ($this->receipt_file) {
        $path = $this->receipt_file->store("receipts/{$this->tenantId}", 'public');
        $receipt = BkReceipt::create([
            'tenant_id'    => $this->tenantId,
            'type'         => $this->receipt_type,
            'date'         => $this->booking_date,
            'net_amount'   => $net,
            'vat_amount'   => $vat,
            'gross_amount' => $gross,
            'currency'     => $this->receipt_currency,
            'file_path'    => $path,
            'meta'         => json_encode(['source' => 'manual']),
        ]);
        $receiptId = $receipt->id;
    }

    // gemeinsame Transaktions-ID
    $transactionId = (string) Str::uuid();

    // Konten laden
    $debit  = Account::where('tenant_id', $this->tenantId)->find($this->debit_account_id);
    $credit = Account::where('tenant_id', $this->tenantId)->find($this->credit_account_id);
    $bank   = Account::where('tenant_id', $this->tenantId)->where('number', '1200')->first();
    $vatAccount      = Account::where('tenant_id', $this->tenantId)->where('number', '1776')->first();
    $inputVatAccount = Account::where('tenant_id', $this->tenantId)->where('number', '1576')->first();

    // Prüfen ob alle notwendigen Konten existieren
    if (!$bank) {
        session()->flash('error', 'Bankkonto (1200) nicht gefunden!');
        return;
    }

    if ($vat > 0 && !$vatAccount && !$inputVatAccount) {
        session()->flash('error', 'Umsatzsteuerkonto (1776) oder Vorsteuerkonto (1576) nicht gefunden!');
        return;
    }

    // Einnahme
    if ($credit && $credit->type === 'revenue') {
        Entry::create([
            'tenant_id'        => $this->tenantId,
            'fiscal_year_id'   => $fiscalYear->id,
            'booking_date'     => $this->booking_date,
            'debit_account_id' => $bank->id,
            'credit_account_id'=> $credit->id,
            'amount'           => $net,
            'description'      => $this->description . ' (Netto)',
            'transaction_id'   => $transactionId,
            'receipt_id'       => $receiptId,
        ]);

        if ($vat > 0 && $vatAccount) {
            Entry::create([
                'tenant_id'        => $this->tenantId,
                'fiscal_year_id'   => $fiscalYear->id,
                'booking_date'     => $this->booking_date,
                'debit_account_id' => $bank->id,
                'credit_account_id'=> $vatAccount->id,
                'amount'           => $vat,
                'description'      => $this->description . ' (USt)',
                'transaction_id'   => $transactionId,
                'receipt_id'       => $receiptId,
            ]);
        }
    }
    // Ausgabe
    else {
        Entry::create([
            'tenant_id'        => $this->tenantId,
            'fiscal_year_id'   => $fiscalYear->id,
            'booking_date'     => $this->booking_date,
            'debit_account_id' => $debit->id,
            'credit_account_id'=> $bank->id,
            'amount'           => $net,
            'description'      => $this->description . ' (Netto)',
            'transaction_id'   => $transactionId,
            'receipt_id'       => $receiptId,
        ]);

        if ($vat > 0 && $inputVatAccount) {
            Entry::create([
                'tenant_id'        => $this->tenantId,
                'fiscal_year_id'   => $fiscalYear->id,
                'booking_date'     => $this->booking_date,
                'debit_account_id' => $inputVatAccount->id,
                'credit_account_id'=> $bank->id,
                'amount'           => $vat,
                'description'      => $this->description . ' (Vorsteuer)',
                'transaction_id'   => $transactionId,
                'receipt_id'       => $receiptId,
            ]);
        }
    }

    // Am Ende der save() Methode:
    session()->flash('success', 'Buchung erfasst!');

    // Nur Popup anzeigen wenn keine identische Vorlage existiert
    if (!$this->templateExists()) {
        $this->showTemplatePrompt = true;
    }

    // Felder zurücksetzen (wie vorher)
    $this->reset([
        'net_amount', 'receipt_file', 'receipt_type'
    ]);

    $this->vat_rate = 19;
    $this->with_vat = false;
    $this->input_mode = 'netto';
    $this->receipt_currency = 'EUR';
}


public function closeTemplatePrompt()
{
    $this->showTemplatePrompt = false;
    // Jetzt das Formular komplett zurücksetzen
    $this->resetForm();
}
public function templateExists()
{
    if (!$this->debit_account_id || !$this->credit_account_id || !$this->description) {
        return false;
    }

    return BkBookingTemplate::where('tenant_id', $this->tenantId)
        ->where('debit_account_id', $this->debit_account_id)
        ->where('credit_account_id', $this->credit_account_id)
        ->where('description', $this->description)
        ->exists();
}

public function getSimilarTemplates()
{
    if (!$this->debit_account_id || !$this->credit_account_id) {
        return collect();
    }

    return BkBookingTemplate::where('tenant_id', $this->tenantId)
        ->where(function($query) {
            $query->where('debit_account_id', $this->debit_account_id)
                  ->orWhere('credit_account_id', $this->credit_account_id);
        })
        ->with(['debitAccount', 'creditAccount'])
        ->get();
}

// Und die resetForm() Methode anpassen:
public function resetForm()
{
    $this->reset([
        'debit_account_id', 'credit_account_id', 'net_amount',
        'description', 'receipt_file', 'receipt_type'
    ]);

    // Diese Felder auf Standardwerte setzen
    $this->vat_rate = 19;
    $this->with_vat = false;
    $this->input_mode = 'netto';
    $this->receipt_currency = 'EUR';
    $this->showTemplatePrompt = false;

    // Booking Date wieder auf Standard setzen
    $this->setDefaultBookingDate();
}

    public function getPreview(): array
    {
        if (! $this->booking_date || ! $this->debit_account_id || ! $this->credit_account_id || ! $this->net_amount) {
            return [];
        }

        [$net, $vat, $gross] = $this->computeAmounts();

        $debit  = Account::where('tenant_id', $this->tenantId)->find($this->debit_account_id);
        $credit = Account::where('tenant_id', $this->tenantId)->find($this->credit_account_id);

        $bank           = Account::where('tenant_id', $this->tenantId)->where('number', '1200')->first();
        $vatAccount     = Account::where('tenant_id', $this->tenantId)->where('number', '1776')->first();
        $inputVatAccount= Account::where('tenant_id', $this->tenantId)->where('number', '1576')->first();

        $rows = [];

        if ($credit && $credit->type === 'revenue') {
            $rows[] = [
                'date'   => $this->booking_date,
                'debit'  => $bank,
                'credit' => $credit,
                'amount' => $net,
                'desc'   => $this->description . ' (Netto)',
            ];
            if ($vat > 0 && $vatAccount) {
                $rows[] = [
                    'date'   => $this->booking_date,
                    'debit'  => $bank,
                    'credit' => $vatAccount,
                    'amount' => $vat,
                    'desc'   => $this->description . ' (USt)',
                ];
            }
        } else {
            $rows[] = [
                'date'   => $this->booking_date,
                'debit'  => $debit,
                'credit' => $bank,
                'amount' => $net,
                'desc'   => $this->description . ' (Netto)',
            ];
            if ($vat > 0 && $inputVatAccount) {
                $rows[] = [
                    'date'   => $this->booking_date,
                    'debit'  => $inputVatAccount,
                    'credit' => $bank,
                    'amount' => $vat,
                    'desc'   => $this->description . ' (Vorsteuer)',
                ];
            }
        }

        return $rows;
    }

    public function updatedInputMode($value)
    {
        if ($value === 'brutto') {
            $this->with_vat = true;
        }
    }

    private function computeAmounts(): array
    {
        $amountInput = (float) $this->net_amount;

        if ($this->input_mode === 'brutto' && $this->with_vat) {
            $gross = round($amountInput, 2);
            $net   = round($gross / (1 + ($this->vat_rate / 100)), 2);
            $vat   = round($gross - $net, 2);
        } else {
            $net   = round($amountInput, 2);
            $vat   = $this->with_vat ? round($net * ($this->vat_rate / 100), 2) : 0.00;
            $gross = round($net + $vat, 2);
        }

        return [$net, $vat, $gross];
    }

public function deleteTemplate($templateId)
{
    try {
        $template = BkBookingTemplate::find($templateId);

        // Prüfen ob der User diese Vorlage löschen darf
        if ($template && !$template->is_global && $template->tenant_id == $this->tenantId) {
            $template->delete();
            $this->loadTemplates();
            session()->flash('success', 'Vorlage wurde gelöscht!');
        } else {
            session()->flash('error', 'Diese Vorlage kann nicht gelöscht werden.');
        }
    } catch (\Exception $e) {
        session()->flash('error', 'Fehler beim Löschen der Vorlage: ' . $e->getMessage());
    }
}


    public function render()
    {
        $tenants = Tenant::where('customer_id', Auth::guard('customer')->id())
            ->orderBy('name')
            ->get();

        $accounts = Account::where('tenant_id', $this->tenantId)
            ->orderBy('number')
            ->get();

        return view('livewire.backend.bookkeeping.entry-form', compact('accounts', 'tenants'))
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
