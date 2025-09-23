<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Imports\OpeningBalancePreviewImport;
use App\Models\Account;
use App\Models\Entry;
use App\Models\FiscalYear;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class OpeningBalanceForm extends Component
{
    use WithFileUploads;

    public $tenantId;

    public $fiscalYearId;

    /**
     * balances speichert je Konto:
     * [
     *   'debit'  => Betrag im Soll,
     *   'credit' => Betrag im Haben
     * ]
     */
    public $balances = [];

    public $file;

    public function mount()
    {
        $tenant = \App\Models\Tenant::where('is_current', true)->first();
        $this->tenantId = $tenant?->id;

        $fy = FiscalYear::current($this->tenantId);
        $this->fiscalYearId = $fy?->id;

        $accounts = Account::where('tenant_id', $this->tenantId)->orderBy('number')->get();
        foreach ($accounts as $acc) {
            $this->balances[$acc->id] = ['debit' => 0.0, 'credit' => 0.0];

        }
    }

    public function save()
    {
        $fiscalYear = FiscalYear::findOrFail($this->fiscalYearId);

        $ebk = Account::firstOrCreate(
            ['tenant_id' => $this->tenantId, 'number' => '9000'],
            ['name' => 'Eröffnungsbilanzkonto', 'type' => 'equity'],
        );

        $totalDebit = 0.0;
        $totalCredit = 0.0;

        // dd($this->balances);

        foreach ($this->balances as $accountId => $row) {
            $debit = max(0, (float) ($row['debit'] ?? 0));   // keine negativen Werte
            $credit = max(0, (float) ($row['credit'] ?? 0));

            if ($debit == 0 && $credit == 0) {
                continue;
            }

            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        // Summen für Anzeige in der View
        session()->flash('totals', [
            'soll' => $totalDebit,
            'haben' => $totalCredit,
            'differenz' => round($totalDebit - $totalCredit, 2),
        ]);

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            session()->flash('error', 'Die Eröffnungsbilanz ist nicht ausgeglichen: Soll ' .
                number_format($totalDebit, 2, ',', '.') . ' € ≠ Haben ' .
                number_format($totalCredit, 2, ',', '.') . ' €');

            return;
        }

        // dd($this->balances);

        foreach ($this->balances as $accountId => $row) {
            $debit = max(0, (float) ($row['debit'] ?? 0));
            $credit = max(0, (float) ($row['credit'] ?? 0));
            if ($debit == 0 && $credit == 0) {
                continue;
            }

            $acc = Account::find($accountId);
            if (! $acc) {
                continue;
            }

            if ($debit > 0) {
                Entry::create([
                    'tenant_id' => $this->tenantId,
                    'fiscal_year_id' => $this->fiscalYearId,
                    'booking_date' => $fiscalYear->start_date,
                    'debit_account_id' => $acc->id,
                    'credit_account_id' => $ebk->id,
                    'amount' => round($debit, 2),
                    'description' => "EB-Eröffnung {$acc->number}",
                ]);
            }
            if ($credit > 0) {
                Entry::create([
                    'tenant_id' => $this->tenantId,
                    'fiscal_year_id' => $this->fiscalYearId,
                    'booking_date' => $fiscalYear->start_date,
                    'debit_account_id' => $ebk->id,
                    'credit_account_id' => $acc->id,
                    'amount' => round($credit, 2),
                    'description' => "EB-Eröffnung {$acc->number}",
                ]);
            }
        }

        session()->flash('success', 'Eröffnungsbilanz erfolgreich erfasst!');
    }

    public function import()
    {
        $this->validate(['file' => 'required|file|mimes:xlsx,csv']);

        $preview = Excel::toCollection(new OpeningBalancePreviewImport, $this->file->getRealPath())->first();
        $newAccounts = 0;

        $header = $preview[0] ?? null;
        $startRow = 0;
        if ($header && is_string($header[0]) && str_contains(mb_strtolower($header[0]), 'konto')) {
            $startRow = 1;
        }

        for ($i = $startRow; $i < count($preview); $i++) {
            $row = $preview[$i];
            $accountNumber = trim((string) ($row[0] ?? ''));
            $accountName = trim((string) ($row[1] ?? ''));
            if ($accountNumber === '' || $accountNumber === '9000') {
                continue;
            }

            $saldoSoll = $this->toFloat($row[12] ?? 0);
            $saldoHaben = $this->toFloat($row[13] ?? 0);

            if ($saldoSoll == 0 && $saldoHaben == 0) {
                continue;
            }

            $account = Account::where('tenant_id', $this->tenantId)
                ->where('number', $accountNumber)
                ->first();

            if (! $account) {
                $account = Account::create([
                    'tenant_id' => $this->tenantId,
                    'number' => $accountNumber,
                    'name' => $accountName !== '' ? $accountName : 'Unbekanntes Konto',
                    'type' => $this->guessType($accountNumber),
                ]);
                $newAccounts++;
            }

            if (in_array($account->type, ['revenue', 'expense'])) {
                continue;
            }

            $this->balances[$account->id] = [
                'debit' => (float) $saldoSoll,
                'credit' => (float) $saldoHaben,
            ];
        }

        $msg = 'Saldenliste importiert – bitte prüfen und speichern.';
        if ($newAccounts > 0) {
            $msg .= " ({$newAccounts} neue Konten angelegt)";
        }
        session()->flash('success', $msg);
    }

    private function toFloat($v): float
    {
        $s = trim((string) $v);
        if ($s === '') {
            return 0.0;
        }
        $s = str_replace(['.', ' '], '', $s);
        $s = str_replace(',', '.', $s);

        return (float) $s;
    }

    private function guessType(string $number): string
    {
        if (str_starts_with($number, '0') || str_starts_with($number, '1')) {
            return 'asset';
        }
        if (str_starts_with($number, '2')) {
            return 'liability';
        }
        if (str_starts_with($number, '3')) {
            return 'equity';
        }
        if (str_starts_with($number, '4') || str_starts_with($number, '8')) {
            return 'revenue';
        }
        if (str_starts_with($number, '5') || str_starts_with($number, '6') || str_starts_with($number, '7')) {
            return 'expense';
        }

        return 'asset';
    }

    public function render()
    {
        $accounts = Account::where('tenant_id', $this->tenantId)->orderBy('number')->get();

        return view('livewire.backend.bookkeeping.opening-balance-form', compact('accounts'))
            ->extends('backend.layouts.backend');
    }
}
