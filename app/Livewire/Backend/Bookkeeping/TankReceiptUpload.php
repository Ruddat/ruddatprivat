<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Entry;
use App\Models\Tenant;
use App\Models\Account;
use Livewire\Component;
use App\Models\BkReceipt;
use App\Models\FiscalYear;
use Illuminate\Support\Str;
use App\Models\InvoiceUpload;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class TankReceiptUpload extends Component
{
    use WithFileUploads;

    public $files = [];
    public $tenantId;
    public $previews = [];

    public function mount()
    {
        $this->tenantId = Tenant::where('customer_id', auth('customer')->id())->value('id');
    }

    public function parseReceipts()
    {
        $this->validate([
            'files.*'  => 'required|mimes:jpg,jpeg,png,pdf|max:5120',
            'tenantId' => 'required|exists:tenants,id',
        ]);

        $this->previews = [];

        foreach ($this->files as $file) {
            $response = Http::asMultipart()
                ->attach('file', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
                ->post(config('services.ocrspace.endpoint', 'https://api.ocr.space/parse/image'), [
                    'apikey'   => config('services.ocrspace.key', env('OCR_SPACE_API_KEY')),
                    'language' => 'ger',
                ]);

            if (!$response->ok()) {
                session()->flash('error', 'OCR API nicht erreichbar: HTTP ' . $response->status());
                continue;
            }

            $json = $response->json();

            if (isset($json['IsErroredOnProcessing']) && $json['IsErroredOnProcessing']) {
                session()->flash('error', 'OCR fehlgeschlagen: ' . implode(', ', $json['ErrorMessage'] ?? []));
                continue;
            }

            $text = $json['ParsedResults'][0]['ParsedText'] ?? '';
            if (empty($text)) {
                session()->flash('warning', 'Keine Textdaten erkannt.');
                continue;
            }

            // --- Datum erkennen ---
            $date = now()->toDateString();
            if (preg_match('/(\d{2}\.\d{2}\.(\d{2}|\d{4}))/', $text, $m)) {
                $dateRaw = $m[1];
                try {
                    $date = strlen($dateRaw) === 8
                        ? Carbon::createFromFormat('d.m.y', $dateRaw)->format('Y-m-d')
                        : Carbon::createFromFormat('d.m.Y', $dateRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    // fallback = heute
                }
            }

            // --- Bruttobetrag ---
            $gross = 0.0;
            if (preg_match('/(Gesamtbetrag|Gesamtpreis|Brutto).*?([\d\.,]+)/i', $text, $m)) {
                $gross = floatval(str_replace(',', '.', str_replace('.', '', $m[2])));
            } elseif (preg_match('/([\d\.,]+)\s?(EUR|€)/i', $text, $m)) {
                $gross = floatval(str_replace(',', '.', str_replace('.', '', $m[1])));
            }

            // --- MwSt. ---
            $vat = 0.0;
            if (preg_match('/(MwSt|USt|Steuer).*?([\d\.,]+)/i', $text, $m)) {
                $vat = floatval(str_replace(',', '.', str_replace('.', '', $m[2])));
            }

            // Netto berechnen
            $net = ($gross > 0 && $vat > 0)
                ? $gross - $vat
                : ($gross > 0 ? round($gross / 1.19, 2) : 0.0);

            if ($vat == 0 && $gross > 0) {
                $vat = $gross - $net;
            }

            // Konten laden
            $vehicleId = Account::where('tenant_id', $this->tenantId)->where('number', '4600')->value('id');
            $vatId     = Account::where('tenant_id', $this->tenantId)->where('number', '1576')->value('id');
            $bankId    = Account::where('tenant_id', $this->tenantId)->where('number', '1200')->value('id');

            $this->previews[] = [
                'file'      => $file,
                'date'      => $date,
                'net'       => $net,
                'vat'       => $vat,
                'gross'     => $gross,
                'vehicleId' => $vehicleId,
                'vatId'     => $vatId,
                'bankId'    => $bankId,
                'rawText'   => $text,
            ];
        }
    }

    public function saveAll()
    {
        foreach ($this->previews as $preview) {
            $year = Carbon::parse($preview['date'])->year;

            $fiscalYear = FiscalYear::firstOrCreate(
                ['tenant_id' => $this->tenantId, 'year' => $year],
                [
                    'start_date' => "{$year}-01-01",
                    'end_date'   => "{$year}-12-31",
                    'is_current' => true,
                    'closed'     => false,
                ]
            );

// Belegdatei speichern
$path = $preview['file']->store("receipts/{$this->tenantId}", 'public');

// Upload-Datensatz anlegen
$receipt = \App\Models\BkReceipt::create([
    'tenant_id'  => $this->tenantId,
    'type'       => 'fuel',             // damit man später unterscheiden kann
    'number'     => null,               // falls es eine Belegnummer gibt
    'date'       => $preview['date'],   // <- wichtig: Feld heißt "date"
    'net_amount' => $preview['net'],
    'vat_amount' => $preview['vat'],
    'gross_amount'=> $preview['gross'],
    'currency'   => 'EUR',
    'file_path'  => $path,
    'meta'       => json_encode(['source' => 'ocr']),
]);

$tx = Str::uuid();

// Fahrzeugkosten
Entry::create([
    'tenant_id'        => $this->tenantId,
    'fiscal_year_id'   => $fiscalYear->id,
    'booking_date'     => $preview['date'],
    'debit_account_id' => $preview['vehicleId'],
    'amount'           => $preview['net'],
    'description'      => "Tankbeleg",
    'transaction_id'   => $tx,
    'receipt_id'       => $receipt->id,
]);

// Vorsteuer
Entry::create([
    'tenant_id'        => $this->tenantId,
    'fiscal_year_id'   => $fiscalYear->id,
    'booking_date'     => $preview['date'],
    'debit_account_id' => $preview['vatId'],
    'amount'           => $preview['vat'],
    'description'      => "Vorsteuer Tankbeleg",
    'transaction_id'   => $tx,
    'receipt_id'       => $receipt->id,
]);

// Bank
Entry::create([
    'tenant_id'         => $this->tenantId,
    'fiscal_year_id'    => $fiscalYear->id,
    'booking_date'      => $preview['date'],
    'credit_account_id' => $preview['bankId'],
    'amount'            => $preview['gross'],
    'description'       => "Zahlung Tankbeleg",
    'transaction_id'    => $tx,
    'receipt_id'        => $receipt->id,
]);
        }

        session()->flash('success', 'Tankrechnungen erfolgreich importiert.');
        $this->reset(['files', 'previews']);
    }

    public function render()
    {
        $accounts = $this->tenantId
            ? Account::where('tenant_id', $this->tenantId)->orderBy('number')->get()
            : collect();

        return view('livewire.backend.bookkeeping.tank-receipt-upload', [
            'accounts' => $accounts,
        ])
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
