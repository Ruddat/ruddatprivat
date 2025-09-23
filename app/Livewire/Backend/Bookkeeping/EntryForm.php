<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Account;
use App\Models\Entry;
use App\Models\FiscalYear;
use App\Models\Tenant;
use Livewire\Component;

class EntryForm extends Component
{
    public $tenantId;

    public $booking_date;

    public $debit_account_id;

    public $credit_account_id;

    public $net_amount;

    public $description;

    public $vat_rate = 19;

    public $with_vat = false;

    public $input_mode = 'netto'; // 'netto' oder 'brutto'

    protected $rules = [
        'booking_date' => 'required|date',
        'debit_account_id' => 'required|integer',
        'credit_account_id' => 'required|integer|different:debit_account_id',
        'net_amount' => 'required|numeric|min:0.01',
        'vat_rate' => 'nullable|numeric|min:0',
        'description' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->tenantId = Tenant::current()?->id;
    }

    public function save()
    {
        $this->validate();

        [$net, $vat, $gross] = $this->computeAmounts();

        $debit = Account::find($this->debit_account_id);
        $credit = Account::find($this->credit_account_id);

        // Bank
        $bank = Account::where('tenant_id', $this->tenantId)->where('number', '1200')->first();

        // Steuerkonten
        $vatAccount = Account::where('tenant_id', $this->tenantId)->where('number', '1776')->first(); // USt
        $inputVatAccount = Account::where('tenant_id', $this->tenantId)->where('number', '1576')->first(); // Vorsteuer

        // Geschäftsjahr ermitteln
        $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
            ->whereDate('start_date', '<=', $this->booking_date)
            ->whereDate('end_date', '>=', $this->booking_date)
            ->first();

        // kein Jahr gefunden → Fehler
        if (! $fiscalYear) {
            session()->flash('error', 'Für das gewählte Datum existiert kein Buchungsjahr!');

            return;
        }

        // geschlossenes Jahr → Fehler
        if ($fiscalYear->closed) {
            session()->flash('error', "Das Buchungsjahr {$fiscalYear->year} ist geschlossen – keine Buchung möglich.");

            return;
        }

        // wenn mehrere offen sein könnten → auf is_current prüfen
        if (! $fiscalYear->is_current) {
            session()->flash('error', "Das Buchungsjahr {$fiscalYear->year} ist nicht als aktuell markiert.");

            return;
        }

        $fiscalYearId = $fiscalYear->id;

        // Einnahme (wenn Haben-Konto Erlös ist)
        if ($credit && $credit->type === 'revenue') {
            // Bank an Erlöse (Netto)
            Entry::create([
                'tenant_id' => $this->tenantId,
                'fiscal_year_id' => $fiscalYearId,
                'booking_date' => $this->booking_date,
                'debit_account_id' => $bank->id,
                'credit_account_id' => $credit->id,
                'amount' => $net,
                'description' => $this->description . ' (Netto)',
            ]);

            // Bank an USt
            if ($vat > 0 && $vatAccount) {
                Entry::create([
                    'tenant_id' => $this->tenantId,
                    'fiscal_year_id' => $fiscalYearId,
                    'booking_date' => $this->booking_date,
                    'debit_account_id' => $bank->id,
                    'credit_account_id' => $vatAccount->id,
                    'amount' => $vat,
                    'description' => $this->description . ' (USt)',
                ]);
            }
        }
        // Ausgabe
        else {
            // Aufwand an Bank (Netto)
            Entry::create([
                'tenant_id' => $this->tenantId,
                'fiscal_year_id' => $fiscalYearId,
                'booking_date' => $this->booking_date,
                'debit_account_id' => $debit->id,
                'credit_account_id' => $bank->id,
                'amount' => $net,
                'description' => $this->description . ' (Netto)',
            ]);

            // Vorsteuer an Bank
            if ($vat > 0 && $inputVatAccount) {
                Entry::create([
                    'tenant_id' => $this->tenantId,
                    'fiscal_year_id' => $fiscalYearId,
                    'booking_date' => $this->booking_date,
                    'debit_account_id' => $inputVatAccount->id,
                    'credit_account_id' => $bank->id,
                    'amount' => $vat,
                    'description' => $this->description . ' (Vorsteuer)',
                ]);
            }
        }

        session()->flash('success', 'Buchung erfasst!');
        $this->reset([
            'booking_date', 'debit_account_id', 'credit_account_id',
            'net_amount', 'vat_rate', 'description', 'with_vat', 'input_mode',
        ]);
    }

    public function getPreview(): array
    {
        if (! $this->booking_date || ! $this->debit_account_id || ! $this->credit_account_id || ! $this->net_amount) {
            return [];
        }

        [$net, $vat, $gross] = $this->computeAmounts();

        $debit = Account::find($this->debit_account_id);
        $credit = Account::find($this->credit_account_id);

        $bank = Account::where('tenant_id', $this->tenantId)->where('number', '1200')->first();
        $vatAccount = Account::where('tenant_id', $this->tenantId)->where('number', '1776')->first();
        $inputVatAccount = Account::where('tenant_id', $this->tenantId)->where('number', '1576')->first();

        $rows = [];

        if ($credit && $credit->type === 'revenue') {
            $rows[] = [
                'date' => $this->booking_date,
                'debit' => $bank,
                'credit' => $credit,
                'amount' => $net,
                'desc' => $this->description . ' (Netto)',
            ];
            if ($vat > 0 && $vatAccount) {
                $rows[] = [
                    'date' => $this->booking_date,
                    'debit' => $bank,
                    'credit' => $vatAccount,
                    'amount' => $vat,
                    'desc' => $this->description . ' (USt)',
                ];
            }
        } else {
            $rows[] = [
                'date' => $this->booking_date,
                'debit' => $debit,
                'credit' => $bank,
                'amount' => $net,
                'desc' => $this->description . ' (Netto)',
            ];
            if ($vat > 0 && $inputVatAccount) {
                $rows[] = [
                    'date' => $this->booking_date,
                    'debit' => $inputVatAccount,
                    'credit' => $bank,
                    'amount' => $vat,
                    'desc' => $this->description . ' (Vorsteuer)',
                ];
            }
        }

        return $rows;
    }

    // 1) Auto-Set: Wenn auf "Brutto" umgestellt wird, aktiviere standardmäßig MwSt.
    public function updatedInputMode($value)
    {
        if ($value === 'brutto') {
            $this->with_vat = true; // die meisten Brutto-Belege enthalten MwSt
        }
    }

    // 2) Zentrale Berechnung an einer Stelle (verhindert Überschreiben)
    private function computeAmounts(): array
    {
        // amountInput = je nach Modus Netto oder Brutto
        $amountInput = (float) $this->net_amount; // wir behalten deinen Property-Namen bei

        if ($this->input_mode === 'brutto' && $this->with_vat) {
            $gross = round($amountInput, 2);
            $net = round($gross / (1 + ($this->vat_rate / 100)), 2);
            $vat = round($gross - $net, 2);
        } else {
            $net = round($amountInput, 2);
            $vat = $this->with_vat ? round($net * ($this->vat_rate / 100), 2) : 0.00;
            $gross = round($net + $vat, 2);
        }

        return [$net, $vat, $gross];
    }

    private function isRevenueBooking(): bool
    {
        $debit = Account::find($this->debit_account_id);

        return $debit && $debit->type === 'asset'; // z. B. Bank im Soll = Einnahme
    }

    public function render()
    {
        $accounts = Account::where('tenant_id', $this->tenantId)
            ->orderBy('number')
            ->get();

        return view('livewire.backend.bookkeeping.entry-form', compact('accounts'))
            ->extends('backend.layouts.backend');
    }
}
