<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Entry;
use App\Models\Account;
use Livewire\Component;
use App\Models\FiscalYear;
use Illuminate\Support\Str;
use App\Models\BkReceipt;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Smalot\PdfParser\Parser;

class InvoiceUploadForm extends Component
{
    use WithFileUploads;

    public $files = [];   // mehrere Dateien
    public $tenantId;
    public $previews = [];

    public function mount()
    {
        $this->tenantId = \App\Models\Tenant::where('customer_id', Auth::guard('customer')->id())->value('id');
    }

    public function parsePdfs()
    {
        $this->validate([
            'files.*' => 'required|mimes:pdf|max:10240',
            'tenantId' => 'required|exists:tenants,id',
        ]);

        $parser = new Parser();
        $this->previews = [];

        foreach ($this->files as $file) {
            $pdf = $parser->parseFile($file->getRealPath());
            $text = $pdf->getText();

            // Rechnungsnummer
            preg_match('/Rechnung Nr\.\s*(\d+)/', $text, $m);
            $number = $m[1] ?? null;

            // Nettobetrag
            $net = $this->parseAmount('/Nettobetrag.*?€\s*([\d\.,]+)/i', $text);

            // Bruttobetrag
            $gross = $this->parseAmount('/Bruttobetrag.*?€\s*([\d\.,]+)/i', $text);

            // MwSt
            $vat = $this->parseAmount('/(MwSt|Umsatzsteuer|Mehrwertsteuer).*?€\s*([\d\.,]+)/i', $text, 2);

            if ($gross == 0.00 && $net > 0) {
                $gross = $net + $vat;
            }
            if ($vat == 0.00 && $gross > 0 && $net > 0) {
                $vat = $gross - $net;
            }

            // Rechnungsdatum
            preg_match('/Rechnung Nr\..*?(\d{2}\.\d{2}\.\d{4})/', $text, $m);
            $date = isset($m[1])
                ? \Carbon\Carbon::createFromFormat('d.m.Y', $m[1])->format('Y-m-d')
                : now()->toDateString();

            // Standardkonten
// Oder noch besser: Flexible Suche
$revenueAccountId = Account::where('tenant_id', $this->tenantId)
    ->whereIn('number', ['4000', '8400', '8300']) // Mehrere mögliche Nummern
    ->first()?->id;
    
            $vatAccountId     = Account::where('tenant_id', $this->tenantId)->where('number', '1776')->value('id');
            $bankAccountId    = Account::where('tenant_id', $this->tenantId)->where('number', '1200')->value('id');

            // Dublettencheck
            $duplicate = BkReceipt::where('tenant_id', $this->tenantId)
                ->where('number', $number)
                ->exists();

            $this->previews[] = [
                'file'            => $file,
                'number'          => $number,
                'date'            => $date,
                'net_amount'      => $net,
                'vat_amount'      => $vat,
                'gross_amount'    => $gross,
                'revenueAccountId'=> $revenueAccountId,
                'vatAccountId'    => $vatAccountId,
                'bankAccountId'   => $bankAccountId,
                'duplicate'       => $duplicate,
            ];
        }
    }

    private function parseAmount($regex, $text, $group = 1): float
    {
        if (preg_match($regex, $text, $m)) {
            return floatval(str_replace(',', '.', str_replace('.', '', $m[$group])));
        }
        return 0.00;
    }

public function saveAll()
{
    $this->validate([
        'tenantId' => 'required|exists:tenants,id',
    ]);

    foreach ($this->previews as $preview) {
        if ($preview['duplicate']) {
            continue;
        }

        $path = $preview['file']->store("receipts/{$this->tenantId}", 'public');

        $receipt = BkReceipt::create([
            'tenant_id'    => $this->tenantId,
            'type'         => $preview['net_amount'] >= 0 ? 'income' : 'expense', // grob nach Vorzeichen
            'number'       => $preview['number'],
            'date'         => $preview['date'],
            'net_amount'   => $preview['net_amount'],
            'vat_amount'   => $preview['vat_amount'],
            'gross_amount' => $preview['gross_amount'],
            'currency'     => 'EUR',
            'file_path'    => $path,
            'meta'         => json_encode(['source' => 'upload']),
        ]);

        // Geschäftsjahr prüfen/erstellen
        $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
            ->whereDate('start_date', '<=', $receipt->date)
            ->whereDate('end_date', '>=', $receipt->date)
            ->first();

        if (! $fiscalYear) {
            $year = \Carbon\Carbon::parse($receipt->date)->year;
            FiscalYear::where('tenant_id', $this->tenantId)->update(['is_current' => false]);

            $fiscalYear = FiscalYear::create([
                'tenant_id'  => $this->tenantId,
                'year'       => $year,
                'start_date' => "{$year}-01-01",
                'end_date'   => "{$year}-12-31",
                'is_current' => true,
                'closed'     => false,
            ]);
            session()->flash('warning', "Für das Jahr {$year} wurde automatisch ein Geschäftsjahr angelegt.");
        }

        if ($fiscalYear) {
            $tx = Str::uuid();

            if ($receipt->type === 'income') {
                // 💰 Einnahme

                // Bank an Erlöse (Netto)
                Entry::create([
                    'tenant_id'        => $this->tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $receipt->date,
                    'debit_account_id' => $preview['bankAccountId'],
                    'credit_account_id'=> $preview['revenueAccountId'],
                    'amount'           => $receipt->net_amount,
                    'description'      => "Erlös {$receipt->number}",
                    'transaction_id'   => $tx,
                    'receipt_id'       => $receipt->id,
                ]);

                // Bank an Umsatzsteuer
                if ($preview['vat_amount'] > 0) {
                    Entry::create([
                        'tenant_id'        => $this->tenantId,
                        'fiscal_year_id'   => $fiscalYear->id,
                        'booking_date'     => $receipt->date,
                        'debit_account_id' => $preview['bankAccountId'],
                        'credit_account_id'=> $preview['vatAccountId'],
                        'amount'           => $preview['vat_amount'],
                        'description'      => "USt {$receipt->number}",
                        'transaction_id'   => $tx,
                        'receipt_id'       => $receipt->id,
                    ]);
                }

            } else {
                // 💸 Ausgabe

                // Aufwendungen an Bank (Netto)
                Entry::create([
                    'tenant_id'        => $this->tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $receipt->date,
                    'debit_account_id' => $preview['revenueAccountId'], // hier eher: Aufwandskonto (z. B. 4000 Wareneinkauf)
                    'credit_account_id'=> $preview['bankAccountId'],
                    'amount'           => $receipt->net_amount,
                    'description'      => "Aufwand {$receipt->number}",
                    'transaction_id'   => $tx,
                    'receipt_id'       => $receipt->id,
                ]);

                // Vorsteuer an Bank
                if ($preview['vat_amount'] > 0) {
                    Entry::create([
                        'tenant_id'        => $this->tenantId,
                        'fiscal_year_id'   => $fiscalYear->id,
                        'booking_date'     => $receipt->date,
                        'debit_account_id' => $preview['vatAccountId'],  // Vorsteuerkonto (1576)
                        'credit_account_id'=> $preview['bankAccountId'],
                        'amount'           => $preview['vat_amount'],
                        'description'      => "Vorsteuer {$receipt->number}",
                        'transaction_id'   => $tx,
                        'receipt_id'       => $receipt->id,
                    ]);
                }
            }
        }
    }

    session()->flash('success', 'Rechnungen importiert. Dubletten wurden automatisch übersprungen.');
    $this->reset(['files','previews']);
}


    public function render()
    {
        $invoices = BkReceipt::where('tenant_id', $this->tenantId)->latest()->get();
        $accounts = Account::where('tenant_id', $this->tenantId)->orderBy('number')->get();

        return view('livewire.backend.bookkeeping.invoice-upload-form', compact('invoices','accounts'))
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
