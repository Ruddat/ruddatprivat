<?php

namespace App\Livewire\Backend\Bookkeeping;

use Carbon\Carbon;
use App\Models\Entry;
use App\Models\Tenant;
use App\Models\Account;
use Livewire\Component;
use App\Models\FiscalYear;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ImportEntries extends Component
{
    use WithFileUploads;

    public $source = 'ms_buchhalter';
    public $file;

    public array $preview = [];   // Vorschau-Daten
    public ?string $filePath = null; // Pfad der temp. Livewire-Datei

    public ?int $tenantId = null;
    public bool $autocreateAccounts = true; // bei Bedarf deaktivieren



    public string $fiscalYearId = 'auto'; // 'auto' oder konkrete ID
    public array $fiscalYearOptions = []; // für Dropdown


    /** Cache, damit wir Konten nicht zigmal suchen/anlegen */
    public array $accountCache = [];

public function mount()
{
    $this->tenantId = Tenant::where('customer_id', Auth::guard('customer')->id())
        ->where('is_current', true)
        ->value('id');

    // Dropdown-Optionen aus vorhandenen Geschäftsjahren
    $years = FiscalYear::where('tenant_id', $this->tenantId)
        ->orderBy('start_date')
        ->get(['id','start_date','end_date']);

    $this->fiscalYearOptions = $years->map(fn($fy) => [
        'id'   => (string)$fy->id,
        'text' => sprintf('%s – %s', 
            \Carbon\Carbon::parse($fy->start_date)->format('d.m.Y'),
            \Carbon\Carbon::parse($fy->end_date)->format('d.m.Y')
        ),
    ])->toArray();
}

    public function updatedFile()
    {
        $this->generatePreview();
    }

    /** -------- Helpers: Parsing / Encoding -------- */

    protected function parseDate(?string $dmy): ?string
    {
        $dmy = $dmy ? trim($dmy) : null;
        if (!$dmy) return null;
        $dmy = preg_replace('/\s+.*/', '', $dmy); // "01.01.2019 00:00" -> "01.01.2019"
        try {
            return Carbon::createFromFormat('d.m.Y', $dmy)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function parseAmount(?string $val): ?float
    {
        if ($val === null) return null;
        $val = trim($val);
        if ($val === '') return null;
        // deutsche Zahl "1.234,56" -> "1234.56"
        $val = str_replace('.', '', $val);   // tausenderpunkte weg
        $val = str_replace(',', '.', $val);  // komma -> punkt
        return is_numeric($val) ? (float)$val : null;
    }

    protected function fixEncoding($value)
    {
        if (is_string($value)) {
            if (!mb_detect_encoding($value, 'UTF-8', true)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
            }
            $value = preg_replace('/^\xEF\xBB\xBF/', '', $value); // BOM
        }
        return $value;
    }

    protected function normalizeRow($header, $row)
    {
        $header = array_map('trim', $header);
        $header = array_map([$this, 'fixEncoding'], $header);

        $hc = count($header);
        $rc = count($row);

        if ($rc < $hc) $row = array_pad($row, $hc, null);
        elseif ($rc > $hc) $row = array_slice($row, 0, $hc);

        $row = array_map([$this, 'fixEncoding'], $row);

        return array_combine($header, $row);
    }

    /** -------- Konto-Auflösung (SKR Nummer -> accounts.id) -------- */

    protected function resolveAccountId(?string $accountNumber): ?int
    {
        if (!$accountNumber) return null;

        $number = trim((string)$accountNumber); // führende Nullen beibehalten (z.B. "0480")

        if (isset($this->accountCache[$number])) {
            return $this->accountCache[$number];
        }

        // HIER ggf. Spaltenname anpassen, falls nicht 'number'
        $accountId = Account::where('tenant_id', $this->tenantId)
            ->where('number', $number) // <- ggf. 'account_number' oder 'code'
            ->value('id');

        if (!$accountId && $this->autocreateAccounts) {
            $acc = Account::create([
                'tenant_id' => $this->tenantId,
                'number'    => $number,          // <- hier ebenso anpassen wenn nötig
                'name'      => 'Auto-Import ' . $number,
                // weitere Pflichtfelder hier setzen, falls dein Model welche hat (z.B. 'skr' => 'skr03', 'type' => null, ...)
            ]);
            $accountId = $acc->id;
        }

        // Wenn immer noch nicht vorhanden und autocreate off -> null zurück (FK bleibt leer & Insert scheitert nicht)
        $this->accountCache[$number] = $accountId ?: null;

        return $this->accountCache[$number];
    }


protected function resolveFiscalYearId(?string $ymd): ?int
{
    if (!$ymd) return null;

    // Manuelle Auswahl hat Vorrang
    if ($this->fiscalYearId !== 'auto' && ctype_digit((string)$this->fiscalYearId)) {
        return (int)$this->fiscalYearId;
    }

    // Auto: Datum in bestehendem FY-Fenster suchen
    $fy = FiscalYear::where('tenant_id', $this->tenantId)
        ->whereDate('start_date', '<=', $ymd)
        ->whereDate('end_date', '>=', $ymd)
        ->first();

    // Fallback: nach Jahr matchen (falls FY exakt Kalenderjahr ist)
    if (!$fy) {
        $y = (int)substr($ymd, 0, 4);
        $fy = FiscalYear::where('tenant_id', $this->tenantId)
            ->where(function($q) use ($y) {
                $q->whereYear('start_date', $y)->orWhereYear('end_date', $y);
            })
            ->first();
    }

    return $fy?->id;
}



    /** -------- Vorschau -------- */

    protected function generatePreview()
    {
        $this->preview = [];
        $this->filePath = null;

        if (!$this->file) return;

        $this->filePath = $this->file->getRealPath();
        $handle = @fopen($this->filePath, 'rb');
        if (!$handle) {
            session()->flash('error', 'Temporäre Datei konnte nicht geöffnet werden.');
            return;
        }

        $firstLine = fgets($handle);
        rewind($handle);

        if (strpos((string)$firstLine, '"Belegdatum";') !== false || substr_count((string)$firstLine, ';') >= substr_count((string)$firstLine, ',')) {
            $header = fgetcsv($handle, 0, ';');
            $delimiter = ';';
        } else {
            $header = fgetcsv($handle, 0, ',');
            $delimiter = ',';
        }

        $cnt = 0;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false && $cnt < 10) {
            if (count($row) === 1 && trim((string)$row[0]) === '') continue;
            $this->preview[] = $this->normalizeRow($header, $row);
            $cnt++;
        }
        fclose($handle);

        if (empty($this->preview)) {
            $this->preview[] = ['Fehler' => 'Keine Daten gefunden – prüfe Encoding oder Trenner.'];
        }
    }

    /** -------- Import -------- */

    public function import()
    {
        if (!$this->filePath || !file_exists($this->filePath)) {
            session()->flash('error', 'Keine Datei zum Import gefunden. Bitte Datei erneut auswählen.');
            return;
        }

        switch ($this->source) {
            case 'ms_buchhalter':
                $this->importMsBuchhalter($this->filePath);
                break;
            default:
                session()->flash('error', 'Unbekannte Quelle.');
        }
    }

    protected function importMsBuchhalter(string $path): void
    {
        $handle = @fopen($path, 'rb');
        if (!$handle) {
            session()->flash('error', 'Datei konnte nicht geöffnet werden.');
            return;
        }

        $firstLine = fgets($handle);
        rewind($handle);

        if (strpos((string)$firstLine, '"Belegdatum";') !== false || substr_count((string)$firstLine, ';') >= substr_count((string)$firstLine, ',')) {
            $header = fgetcsv($handle, 0, ';');
            $delimiter = ';';
        } else {
            $header = fgetcsv($handle, 0, ',');
            $delimiter = ',';
        }

        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            try {
                if (count($row) === 1 && trim((string)$row[0]) === '') continue;

                $data = $this->normalizeRow($header, $row);

                // Pflichtfelder checken
                if (empty($data['Buchungsdatum']) || empty($data['Buchungsbetrag'])) {
                    $errors[] = "Zeile ohne Buchungsdatum oder Betrag übersprungen";
                    continue;
                }

                $date   = $this->parseDate($data['Buchungsdatum'] ?? null);
                $amount = $this->parseAmount($data['Buchungsbetrag'] ?? null);

                // **WICHTIG**: Kontonummern -> Account-IDs
                $debitId  = $this->resolveAccountId($data['Sollkonto']  ?? null);
                $creditId = $this->resolveAccountId($data['Habenkonto'] ?? null);

                // Wenn Accounts nicht existieren und autocreate aus: Zeile überspringen
                if (!$debitId || !$creditId) {
                    $errors[] = "Konto nicht gefunden (Soll: " . (isset($data['Sollkonto']) ? $data['Sollkonto'] : '-') . " / Haben: " . (isset($data['Habenkonto']) ? $data['Habenkonto'] : '-') . ")";
                    continue;
                }
$dateYmd = $this->parseDate($data['Buchungsdatum'] ?? null);
$amount  = $this->parseAmount($data['Buchungsbetrag'] ?? null);


                Entry::create([
                    'tenant_id'         => $this->tenantId,
                    'fiscal_year_id'    => $this->resolveFiscalYearId($dateYmd), // <<< NEU
                    'booking_date'      => $date,
                    'debit_account_id'  => $debitId,
                    'credit_account_id' => $creditId,
                    'amount'            => $amount,
                    'description'       => $data['Buchungstext'] ?? null,
                    'document_number'   => $data['Belegnummer'] ?? null,
                    'currency'          => $data['Währung'] ?? 'EUR',
                ]);

                $imported++;
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
                Log::error('Import Fehler', ['exception' => $e, 'row' => $row]);
            }
        }

        fclose($handle);

        if ($imported > 0) {
            session()->flash('success', "{$imported} Buchungen erfolgreich importiert.");
        }
        if (!empty($errors)) {
            session()->flash('warning', "Einige Zeilen konnten nicht importiert werden: " . implode(' | ', array_slice($errors, 0, 5)));
        }

        // optional: Reset nach Import
        $this->reset(['file', 'preview', 'filePath', 'accountCache']);
    }

    public function render()
    {
        return view('livewire.backend.bookkeeping.import-entries')
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
