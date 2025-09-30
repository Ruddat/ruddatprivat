<?php

namespace App\Livewire\Backend\Bookkeeping;

use Carbon\Carbon;
use App\Models\Entry;
use App\Models\Tenant;
use App\Models\Account;
use Livewire\Component;
use App\Models\FiscalYear;
use Illuminate\Support\Str;
use App\Models\BkBalanceGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportBalanceSheet extends Component
{
    public $tenantId;
    public $date;
    public $page = 1;
    public $skr = 'skr03';

    // Neue Properties für bessere UX
    public $showExplanation = false;
    public $closingInProgress = false;

    protected $listeners = ['refreshBalanceSheet' => '$refresh'];

    public function mount()
    {
        $this->tenantId = Tenant::where('customer_id', Auth::guard('customer')->id())
            ->where('is_current', true)
            ->value('id');

        $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
            ->where('is_current', true)
            ->first();

        $this->date = $fiscalYear?->end_date ?? Carbon::now()->endOfYear()->toDateString();
    }

    public function setPage($page) { $this->page = $page; }

    /** Erklärende Informationen anzeigen/ausblenden */
    public function toggleExplanation()
    {
        $this->showExplanation = !$this->showExplanation;
    }

    /** Aktuelles Jahr abschließen */
    public function closeYear()
    {
        // Dispatch a browser event for confirmation, handle response in JS
        $this->dispatch('confirmAction', [
            'message' => 'Möchten Sie das aktuelle Geschäftsjahr wirklich abschließen? Diese Aktion kann nicht rückgängig gemacht werden.',
            'action' => 'closeYearConfirmed'
        ]);
      //  return;

        $this->closingInProgress = true;
        $this->performClosing('current');
        $this->closingInProgress = false;
    }

    /** Vorjahr abschließen */
    public function closePreviousYear()
    {
        // Dispatch a browser event for confirmation, handle response in JS
        $this->dispatch('confirmAction', [
            'message' => 'Möchten Sie das Vorjahr wirklich abschließen? Diese Aktion kann nicht rückgängig gemacht werden.',
            'action' => 'closePreviousYearConfirmed'
        ]);
        return;

        $this->closingInProgress = true;
        $this->performClosing('previous');
        $this->closingInProgress = false;
    }

    /** Gemeinsame Abschluss-Logik */
    private function performClosing($type)
    {
        try {
            DB::transaction(function () use ($type) {
                if ($type === 'current') {
                    $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
                        ->where('is_current', true)
                        ->firstOrFail();
                } else {
                    $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
                        ->where('closed', false)
                        ->where('is_current', false)
                        ->orderByDesc('year')
                        ->firstOrFail();
                }

                // Prüfe ob Stichtag korrekt ist
                if (!Carbon::parse($this->date)->isSameDay(Carbon::parse($fiscalYear->end_date))) {
                    throw new \Exception("Der Jahresabschluss ist nur zum Stichtag {$fiscalYear->end_date} möglich.");
                }

                // Doppelten Abschluss verhindern
                if ($fiscalYear->closed) {
                    throw new \Exception("Das Geschäftsjahr {$fiscalYear->year} ist bereits abgeschlossen.");
                }

                // GuV berechnen
                $result = $this->calculateAnnualResult($fiscalYear->id);

                if (abs($result) < 0.01) {
                    session()->flash('info', "Kein Ergebnis für {$fiscalYear->year} (0,00 €). Das Jahr wurde trotzdem geschlossen.");
                } else {
                    // Abschlussbuchung durchführen
                    $this->createClosingEntry($fiscalYear, $result);
                }

                // Jahr schließen
                $fiscalYear->update(['closed' => true]);

                $resultFormatted = number_format($result, 2, ',', '.');
                $message = $result != 0
                    ? "Jahresabschluss für {$fiscalYear->year} gebucht ({$resultFormatted} € " . ($result > 0 ? 'Gewinn' : 'Verlust') . ")."
                    : "Jahresabschluss für {$fiscalYear->year} ohne Ergebnis gebucht.";

                session()->flash('success', $message);
                $this->dispatch('refreshBalanceSheet');
            });
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    /** Jahresergebnis berechnen */
    private function calculateAnnualResult($fiscalYearId)
    {
        $revenue = Entry::where('tenant_id', $this->tenantId)
            ->where('fiscal_year_id', $fiscalYearId)
            ->whereHas('creditAccount', fn($q) => $q->where('type', 'revenue'))
            ->sum('amount');

        $expenses = Entry::where('tenant_id', $this->tenantId)
            ->where('fiscal_year_id', $fiscalYearId)
            ->whereHas('debitAccount', fn($q) => $q->where('type', 'expense'))
            ->sum('amount');

        return $revenue - $expenses;
    }

    /** Abschlussbuchung erstellen */
    /** Abschlussbuchung erstellen - KORRIGIERT */
    private function createClosingEntry($fiscalYear, $result)
    {
        // GuV-Konto (z.B. 8020) sicherstellen
        $guvAccount = Account::firstOrCreate(
            ['tenant_id' => $this->tenantId, 'number' => '8020'],
            ['name' => 'GuV-Konto', 'type' => 'equity']
        );

        $equityAccount = Account::firstOrCreate(
            ['tenant_id' => $this->tenantId, 'number' => '0860'],
            ['name' => 'Eigenkapital', 'type' => 'equity']
        );

        // ================================
        // 1. Ertragskonten abschließen -> Ertrag an GuV
        // ================================
        $revenueAccounts = Entry::where('tenant_id', $this->tenantId)
            ->where('fiscal_year_id', $fiscalYear->id)
            ->whereHas('creditAccount', fn($q) => $q->where('type', 'revenue'))
            ->groupBy('credit_account_id')
            ->select('credit_account_id', DB::raw('SUM(amount) as total'))
            ->get();

        foreach ($revenueAccounts as $rev) {
            if (abs($rev->total) > 0.01) { // Nur Buchungen für nicht-Null-Beträge
                Entry::create([
                    'tenant_id'        => $this->tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $fiscalYear->end_date,
                    'debit_account_id' => $rev->credit_account_id, // Ertragskonto ins Soll
                    'credit_account_id' => $guvAccount->id,        // GuV ins Haben
                    'amount'           => $rev->total,
                    'description'      => "Abschluss Ertrag {$fiscalYear->year}",
                    'transaction_id'   => Str::uuid(),
                ]);
            }
        }

        // ================================
        // 2. Aufwandskonten abschließen -> GuV an Aufwand
        // ================================
        $expenseAccounts = Entry::where('tenant_id', $this->tenantId)
            ->where('fiscal_year_id', $fiscalYear->id)
            ->whereHas('debitAccount', fn($q) => $q->where('type', 'expense'))
            ->groupBy('debit_account_id')
            ->select('debit_account_id', DB::raw('SUM(amount) as total'))
            ->get();

        foreach ($expenseAccounts as $exp) {
            if (abs($exp->total) > 0.01) { // Nur Buchungen für nicht-Null-Beträge
                Entry::create([
                    'tenant_id'        => $this->tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $fiscalYear->end_date,
                    'debit_account_id' => $guvAccount->id,         // GuV ins Soll
                    'credit_account_id' => $exp->debit_account_id, // Aufwand ins Haben
                    'amount'           => $exp->total,
                    'description'      => "Abschluss Aufwand {$fiscalYear->year}",
                    'transaction_id'   => Str::uuid(),
                ]);
            }
        }

   // ================================
    // 2b. Umsatzsteuer / Vorsteuer abschließen
    // ================================
    $ustAccount = Account::firstOrCreate(
        ['tenant_id' => $this->tenantId, 'number' => '476'],
        ['name' => 'Umsatzsteuer 19%', 'type' => 'liability']
    );

    $vstAccount = Account::firstOrCreate(
        ['tenant_id' => $this->tenantId, 'number' => '466'],
        ['name' => 'Vorsteuer 19%', 'type' => 'asset']
    );

    $steuerVerrechnung = Account::firstOrCreate(
        ['tenant_id' => $this->tenantId, 'number' => '1790'],
        ['name' => 'USt-Verrechnung', 'type' => 'liability']
    );

    // USt-Saldo berechnen
    $ustSum = Entry::where('tenant_id', $this->tenantId)
        ->where('fiscal_year_id', $fiscalYear->id)
        ->where(function($q) use ($ustAccount) {
            $q->where('debit_account_id', $ustAccount->id)
              ->orWhere('credit_account_id', $ustAccount->id);
        })
        ->sum(DB::raw("CASE WHEN debit_account_id = {$ustAccount->id} THEN amount ELSE -amount END"));

    // VSt-Saldo berechnen
    $vstSum = Entry::where('tenant_id', $this->tenantId)
        ->where('fiscal_year_id', $fiscalYear->id)
        ->where(function($q) use ($vstAccount) {
            $q->where('debit_account_id', $vstAccount->id)
              ->orWhere('credit_account_id', $vstAccount->id);
        })
        ->sum(DB::raw("CASE WHEN debit_account_id = {$vstAccount->id} THEN amount ELSE -amount END"));

    // USt-Konto ausgleichen -> an Verrechnung
    if (abs($ustSum) > 0.01) {
        $direction = $ustSum < 0 ? 'credit' : 'debit'; // Soll, wenn positiv; Haben, wenn negativ
        Entry::create([
            'tenant_id'        => $this->tenantId,
            'fiscal_year_id'   => $fiscalYear->id,
            'booking_date'     => $fiscalYear->end_date,
            'debit_account_id' => $direction === 'debit' ? $ustAccount->id : $steuerVerrechnung->id,
            'credit_account_id' => $direction === 'debit' ? $steuerVerrechnung->id : $ustAccount->id,
            'amount'           => abs($ustSum),
            'description'      => "Abschluss Umsatzsteuer {$fiscalYear->year}",
            'transaction_id'   => Str::uuid(),
        ]);
    }

    // VSt-Konto ausgleichen -> Verrechnung an VSt
    if (abs($vstSum) > 0.01) {
        $direction = $vstSum < 0 ? 'credit' : 'debit'; // Soll, wenn positiv; Haben, wenn negativ
        Entry::create([
            'tenant_id'        => $this->tenantId,
            'fiscal_year_id'   => $fiscalYear->id,
            'booking_date'     => $fiscalYear->end_date,
            'debit_account_id' => $direction === 'debit' ? $steuerVerrechnung->id : $vstAccount->id,
            'credit_account_id' => $direction === 'debit' ? $vstAccount->id : $steuerVerrechnung->id,
            'amount'           => abs($vstSum),
            'description'      => "Abschluss Vorsteuer {$fiscalYear->year}",
            'transaction_id'   => Str::uuid(),
        ]);
    }

        // ================================
        // 3. GuV-Saldo -> Eigenkapital
        // ================================
        if ($result > 0) {
            // Gewinn: GuV an EK
            Entry::create([
                'tenant_id'        => $this->tenantId,
                'fiscal_year_id'   => $fiscalYear->id,
                'booking_date'     => $fiscalYear->end_date,
                'debit_account_id' => $guvAccount->id,
                'credit_account_id' => $equityAccount->id,
                'amount'           => abs($result),
                'description'      => "Jahresabschluss {$fiscalYear->year} | Gewinn",
                'transaction_id'   => Str::uuid(),
            ]);
        } else {
            // Verlust: EK an GuV
            Entry::create([
                'tenant_id'        => $this->tenantId,
                'fiscal_year_id'   => $fiscalYear->id,
                'booking_date'     => $fiscalYear->end_date,
                'debit_account_id' => $equityAccount->id,
                'credit_account_id' => $guvAccount->id,
                'amount'           => abs($result),
                'description'      => "Jahresabschluss {$fiscalYear->year} | Verlust",
                'transaction_id'   => Str::uuid(),
            ]);
        }
    }





    /** Abschluss rückgängig machen */
    public function rollbackClosing($yearId)
    {
        $this->dispatch('confirmAction', [
            'message' => 'Möchten Sie den Jahresabschluss wirklich rückgängig machen? Alle Abschlussbuchungen werden gelöscht.',
            'action' => 'rollbackClosingConfirmed',
            'yearId' => $yearId
        ]);
     //   return;

        try {
            DB::transaction(function () use ($yearId) {
                $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
                    ->where('id', $yearId)
                    ->firstOrFail();

                $deletedCount = Entry::where('tenant_id', $this->tenantId)
                    ->where('fiscal_year_id', $fiscalYear->id)
                    ->where('description', 'like', "%Jahresabschluss {$fiscalYear->year}%")
                    ->delete();

                if ($deletedCount > 0) {
                    $fiscalYear->update(['closed' => false]);
                    session()->flash('success', "Abschluss für {$fiscalYear->year} rückgängig gemacht ({$deletedCount} Buchungen gelöscht).");
                    $this->dispatch('refreshBalanceSheet');
                } else {
                    session()->flash('warning', "Keine Abschlussbuchungen für {$fiscalYear->year} gefunden.");
                }
            });
        } catch (\Exception $e) {
            session()->flash('error', "Fehler beim Rückgängigmachen: " . $e->getMessage());
        }
    }



/** SOFORT-KORREKTUR - Falsche Buchung löschen und korrekt neu buchen */
public function immediateFix()
{
    try {
        DB::transaction(function () {
            $year = 2024; // ggf. dynamisch übergeben
            $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
                ->where('year', $year)
                ->firstOrFail();

            // 1. Alle bisherigen Abschlussbuchungen löschen
            $deletedCount = Entry::where('tenant_id', $this->tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->where(function ($q) use ($year) {
                    $q->where('description', 'like', "%Jahresabschluss {$year}%")
                      ->orWhere('description', 'like', "%Abschluss Ertrag {$year}%")
                      ->orWhere('description', 'like', "%Abschluss Aufwand {$year}%")
                      ->orWhere('description', 'like', "%Abschluss Umsatzsteuer {$year}%")
                      ->orWhere('description', 'like', "%Abschluss Vorsteuer {$year}%");
                })
                ->delete();

            session()->flash('info', "{$deletedCount} alte Abschlussbuchungen gelöscht.");

            // 2. Ergebnis berechnen
            $revenue = Entry::where('tenant_id', $this->tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->whereHas('creditAccount', fn($q) => $q->where('type', 'revenue'))
                ->sum('amount');

            $expenses = Entry::where('tenant_id', $this->tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->whereHas('debitAccount', fn($q) => $q->where('type', 'expense'))
                ->sum('amount');

            $result = $revenue - $expenses;

            // 3. GuV- und Eigenkapital-/Steuerkonten holen oder anlegen
            $guvAccount = Account::firstOrCreate(
                ['tenant_id' => $this->tenantId, 'number' => '8020'],
                ['name' => 'GuV-Konto', 'type' => 'equity']
            );

            $equityAccount = Account::firstOrCreate(
                ['tenant_id' => $this->tenantId, 'number' => '0860'],
                ['name' => 'Eigenkapital', 'type' => 'equity']
            );

            $ustAccount = Account::firstOrCreate(
                ['tenant_id' => $this->tenantId, 'number' => '476'], // Angepasst
                ['name' => 'Umsatzsteuer 19%', 'type' => 'liability']
            );

            $vstAccount = Account::firstOrCreate(
                ['tenant_id' => $this->tenantId, 'number' => '466'], // Angepasst
                ['name' => 'Vorsteuer 19%', 'type' => 'asset']
            );

            $steuerVerrechnung = Account::firstOrCreate(
                ['tenant_id' => $this->tenantId, 'number' => '1790'],
                ['name' => 'USt-Verrechnung', 'type' => 'liability']
            );

            // 4. Ertragskonten abschließen -> Ertrag an GuV
            $revenueAccounts = Entry::where('tenant_id', $this->tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->whereHas('creditAccount', fn($q) => $q->where('type', 'revenue'))
                ->groupBy('credit_account_id')
                ->select('credit_account_id', DB::raw('SUM(amount) as total'))
                ->get();

            foreach ($revenueAccounts as $rev) {
                if (abs($rev->total) > 0.01) {
                    Entry::create([
                        'tenant_id'        => $this->tenantId,
                        'fiscal_year_id'   => $fiscalYear->id,
                        'booking_date'     => $fiscalYear->end_date,
                        'debit_account_id' => $rev->credit_account_id,
                        'credit_account_id' => $guvAccount->id,
                        'amount'           => $rev->total,
                        'description'      => "Abschluss Ertrag {$fiscalYear->year}",
                        'transaction_id'   => Str::uuid(),
                    ]);
                }
            }

            // 5. Aufwandskonten abschließen -> GuV an Aufwand
            $expenseAccounts = Entry::where('tenant_id', $this->tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->whereHas('debitAccount', fn($q) => $q->where('type', 'expense'))
                ->groupBy('debit_account_id')
                ->select('debit_account_id', DB::raw('SUM(amount) as total'))
                ->get();

            foreach ($expenseAccounts as $exp) {
                if (abs($exp->total) > 0.01) {
                    Entry::create([
                        'tenant_id'        => $this->tenantId,
                        'fiscal_year_id'   => $fiscalYear->id,
                        'booking_date'     => $fiscalYear->end_date,
                        'debit_account_id' => $guvAccount->id,
                        'credit_account_id' => $exp->debit_account_id,
                        'amount'           => $exp->total,
                        'description'      => "Abschluss Aufwand {$fiscalYear->year}",
                        'transaction_id'   => Str::uuid(),
                    ]);
                }
            }

            // 6. Steuerkonten abschließen (USt/VSt -> 1790)
            $ustSum = Entry::where('tenant_id', $this->tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->where(function($q) use ($ustAccount) {
                    $q->where('debit_account_id', $ustAccount->id)
                      ->orWhere('credit_account_id', $ustAccount->id);
                })
                ->sum(DB::raw("CASE WHEN debit_account_id = {$ustAccount->id} THEN amount ELSE -amount END"));

            $vstSum = Entry::where('tenant_id', $this->tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->where(function($q) use ($vstAccount) {
                    $q->where('debit_account_id', $vstAccount->id)
                      ->orWhere('credit_account_id', $vstAccount->id);
                })
                ->sum(DB::raw("CASE WHEN debit_account_id = {$vstAccount->id} THEN amount ELSE -amount END"));

            if (abs($ustSum) > 0.01) {
                Entry::create([
                    'tenant_id'        => $this->tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $fiscalYear->end_date,
                    'debit_account_id' => $ustAccount->id,
                    'credit_account_id'=> $steuerVerrechnung->id,
                    'amount'           => abs($ustSum),
                    'description'      => "Abschluss Umsatzsteuer {$fiscalYear->year}",
                    'transaction_id'   => Str::uuid(),
                ]);
            }

            if (abs($vstSum) > 0.01) {
                Entry::create([
                    'tenant_id'        => $this->tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $fiscalYear->end_date,
                    'debit_account_id' => $steuerVerrechnung->id,
                    'credit_account_id'=> $vstAccount->id,
                    'amount'           => abs($vstSum),
                    'description'      => "Abschluss Vorsteuer {$fiscalYear->year}",
                    'transaction_id'   => Str::uuid(),
                ]);
            }

            // 7. GuV-Saldo ins Eigenkapital
            if ($result > 0) {
                Entry::create([
                    'tenant_id'        => $this->tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $fiscalYear->end_date,
                    'debit_account_id' => $guvAccount->id,
                    'credit_account_id'=> $equityAccount->id,
                    'amount'           => abs($result),
                    'description'      => "Jahresabschluss {$fiscalYear->year} | Gewinn",
                    'transaction_id'   => Str::uuid(),
                ]);
            } else {
                Entry::create([
                    'tenant_id'        => $this->tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $fiscalYear->end_date,
                    'debit_account_id' => $equityAccount->id,
                    'credit_account_id'=> $guvAccount->id,
                    'amount'           => abs($result),
                    'description'      => "Jahresabschluss {$fiscalYear->year} | Verlust",
                    'transaction_id'   => Str::uuid(),
                ]);
            }

            session()->flash(
                'success',
                "Jahresabschluss {$fiscalYear->year} korrekt gebucht: " .
                number_format($result, 2, ',', '.') . " € " .
                ($result > 0 ? 'Gewinn' : 'Verlust')
            );
        });
    } catch (\Exception $e) {
        session()->flash('error', "Korrektur fehlgeschlagen: " . $e->getMessage());
    }
}






    public function render()
    {
        $data = $this->getBalanceSheetData();

        return view('livewire.backend.bookkeeping.report-balance-sheet', array_merge($data, [
            'date'        => $this->date,
            'page'        => $this->page,
            'showExplanation' => $this->showExplanation,
            'closingInProgress' => $this->closingInProgress,
        ]))->extends('backend.customer.layouts.app')
          ->section('content');
    }

    /** Bilanzdaten berechnen */
private function getBalanceSheetData()
{
    $entries = Entry::with(['debitAccount', 'creditAccount'])
        ->where('tenant_id', $this->tenantId)
        ->whereDate('booking_date', '<=', $this->date)
        ->get();

    $balances = $this->calculateAccountBalances($entries);
    $groupedBalances = $this->groupBalancesByBalanceGroups($balances);
    $totals = $this->calculateBalanceSheetTotals($groupedBalances);

    $currentYear = FiscalYear::where('tenant_id', $this->tenantId)
        ->where('is_current', true)
        ->first();

    // Nur wenn offen: Gewinn/Verlust berechnen
    $profitLoss = $this->calculateProfitLoss($balances, $currentYear);

    return [
        'groups'      => $groupedBalances,
        'totals'      => $totals,
        'fiscalYear'  => $currentYear,
        'profitLoss'  => $profitLoss,
    ];
}

    /** Kontosalden berechnen */
/** Kontosalden berechnen - INKLUSIVE Erfolgskonten für Bilanzprüfung */
/** Korrigierte Salden-Berechnung */
private function calculateAccountBalances($entries)
{
    $balances = [];

    $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
        ->where('is_current', true)
        ->first();
    $endDate = $fiscalYear ? $fiscalYear->end_date : $this->date;

    foreach ($entries as $entry) {
        if (Carbon::parse($entry->booking_date)->lte(Carbon::parse($endDate))) {
            if ($entry->debitAccount) {
                $acc = $entry->debitAccount;
                $balances[$acc->id] = $balances[$acc->id] ?? [
                    'account' => $acc,
                    'balance' => 0
                ];
                $balances[$acc->id]['balance'] += $entry->amount;
            }
            if ($entry->creditAccount) {
                $acc = $entry->creditAccount;
                $balances[$acc->id] = $balances[$acc->id] ?? [
                    'account' => $acc,
                    'balance' => 0
                ];
                $balances[$acc->id]['balance'] -= $entry->amount;
            }
        }
    }

    return $balances;
}

/** GuV für Bilanzprüfung berechnen */
/** GuV nur anzeigen, wenn das Geschäftsjahr noch offen ist */
private function calculateProfitLoss($balances, $fiscalYear)
{
    if ($fiscalYear->closed) {
        return 0;
    }

    $revenue = 0;
    $expenses = 0;

    foreach ($balances as $balance) {
        if ($balance['account']->type === 'revenue') {
            $revenue += -$balance['balance'];  // Negatives Saldo → positives Ertrag
        } elseif ($balance['account']->type === 'expense') {
            $expenses += $balance['balance'];  // Positives Saldo → positiver Aufwand
        }
    }

    return $revenue - $expenses;
}

    /** Salden nach Bilanzgruppen gruppieren */
/** Korrigierte Bilanz-Gruppierung */
/** Korrigierte Bilanz-Gruppierung */
private function groupBalancesByBalanceGroups($balances)
{
    $groupedBalances = [
        'asset' => ['A' => ['label' => 'Aktiva', 'balance' => 0, 'accounts' => []]],
        'liability' => ['P1' => ['label' => 'Verbindlichkeiten', 'balance' => 0, 'accounts' => []]],
    ];

    $currentYear = FiscalYear::where('tenant_id', $this->tenantId)
        ->where('is_current', true)
        ->first();
    $isClosed = $currentYear ? $currentYear->closed : false;

    if ($isClosed) {
        $groupedBalances['equity'] = ['P2' => ['label' => 'Eigenkapital', 'balance' => 0, 'accounts' => []]];
    }

    foreach ($balances as $balance) {
        $type = $balance['account']->type;
        if ($type === 'asset') {
            $groupedBalances['asset']['A']['balance'] += $balance['balance'];
            $groupedBalances['asset']['A']['accounts'][] = $balance;
        } elseif ($type === 'liability') {
            $groupedBalances['liability']['P1']['balance'] += $balance['balance'];
            $groupedBalances['liability']['P1']['accounts'][] = $balance;
        } elseif ($type === 'equity' && $isClosed) {
            $groupedBalances['equity']['P2']['balance'] += $balance['balance'];
            $groupedBalances['equity']['P2']['accounts'][] = $balance;
        }
        // Revenue/Expense ignorieren – gehören nicht in Bilanz
    }

    // Entferne leere Gruppen, falls nötig
    foreach ($groupedBalances as $key => $groups) {
        foreach ($groups as $subKey => $group) {
            if (empty($group['accounts'])) {
                unset($groupedBalances[$key][$subKey]);
            }
        }
    }

    return $groupedBalances;
}


    /** Bilanzsummen berechnen */
private function calculateBalanceSheetTotals($groupedBalances)
{
    $sumAktiva = collect($groupedBalances['asset'] ?? [])->sum('balance');  // Positiv
    $sumPassiva = collect($groupedBalances['liability'] ?? [])->sum('balance')
                + collect($groupedBalances['equity'] ?? [])->sum('balance');  // Negativ

    $difference = $sumAktiva + $sumPassiva;  // Pos + Neg = 0, wenn balanced (offene GuV ignoriert)

    return [
        'aktiva' => $sumAktiva,
        'passiva' => -$sumPassiva,  // Für Anzeige positiv machen
        'difference' => $difference,
        'isBalanced' => abs($difference) < 0.01,
    ];
}
}
