<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Entry;
use App\Models\Account;
use Livewire\Component;
use App\Models\FiscalYear;
use Livewire\WithFileUploads;
use Smalot\PdfParser\Parser; // PDF-Parser


class InvoiceUpload extends Component
{
    use WithFileUploads;

    public $file;
    public $tenantId;

    public function mount()
    {
        $this->tenantId = session('tenant_id'); // oder Tenant::current()
    }

    public function save()
    {
        $this->validate([
            'file' => 'required|mimes:pdf|max:10240', // 10MB
        ]);

        // PDF parsen
        $parser = new Parser();
        $pdf = $parser->parseFile($this->file->getRealPath());
        $text = $pdf->getText();

        // Werte extrahieren
        preg_match('/Rechnung Nr\.\s*(\d+)/', $text, $m);
        $invoiceNumber = $m[1] ?? null;

        preg_match('/Nettobetrag.*?([\d\.,]+)/', $text, $m);
        $net = isset($m[1]) ? str_replace(',', '.', str_replace('.', '', $m[1])) : 0;

        preg_match('/MwSt.*?([\d\.,]+)/', $text, $m);
        $vat = isset($m[1]) ? str_replace(',', '.', str_replace('.', '', $m[1])) : 0;

        preg_match('/Bruttobetrag.*?(-?[\d\.,]+)/', $text, $m);
        $gross = isset($m[1]) ? str_replace(',', '.', str_replace('.', '', $m[1])) : 0;

        preg_match('/Rechnung Nr\..*?(\d{2}\.\d{2}\.\d{4})/', $text, $m);
        $date = $m[1] ?? now()->toDateString();

        // Datei speichern
        $path = $this->file->store('invoices/'.$this->tenantId, 'public');

        $invoice = InvoiceUpload::create([
            'tenant_id' => $this->tenantId,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => \Carbon\Carbon::parse($date),
            'net_amount' => $net,
            'vat_amount' => $vat,
            'gross_amount' => $gross,
            'file_path' => $path,
        ]);

        // Geschäftsjahr
        $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
            ->whereDate('start_date', '<=', $invoice->invoice_date)
            ->whereDate('end_date', '>=', $invoice->invoice_date)
            ->first();

        if ($fiscalYear) {
            $vendor = Account::where('tenant_id', $this->tenantId)->where('number', '1600')->first(); // Kreditor
            $expense = Account::where('tenant_id', $this->tenantId)->where('number', '4900')->first(); // Provision
            $inputVat = Account::where('tenant_id', $this->tenantId)->where('number', '1576')->first(); // Vorsteuer

            // Aufwand buchen
            Entry::create([
                'tenant_id' => $this->tenantId,
                'fiscal_year_id' => $fiscalYear->id,
                'booking_date' => $invoice->invoice_date,
                'debit_account_id' => $expense->id,
                'credit_account_id' => $vendor->id,
                'amount' => $invoice->net_amount,
                'description' => "Provision Just Deliver {$invoice->invoice_number}",
            ]);

            // Vorsteuer buchen
            Entry::create([
                'tenant_id' => $this->tenantId,
                'fiscal_year_id' => $fiscalYear->id,
                'booking_date' => $invoice->invoice_date,
                'debit_account_id' => $inputVat->id,
                'credit_account_id' => $vendor->id,
                'amount' => $invoice->vat_amount,
                'description' => "Vorsteuer Just Deliver {$invoice->invoice_number}",
            ]);
        }

        session()->flash('success', 'Rechnung importiert und gebucht.');
        $this->reset('file');
    }

    public function render()
    {
        $invoices = InvoiceUpload::where('tenant_id', $this->tenantId)->latest()->get();

        return view('livewire.backend.bookkeeping.invoice-upload', compact('invoices'))
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}