<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Entry;
use App\Models\Account;
use App\Models\FiscalYear;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CloseFiscalYear extends Command
{
    protected $signature = 'bookkeeping:close-year {tenantId} {year} {--dry-run}';
    protected $description = 'Schließt das Geschäftsjahr ab und eröffnet das neue Jahr';

    public function handle()
    {
        $tenantId = $this->argument('tenantId');
        $year = $this->argument('year');
        $dryRun = $this->option('dry-run');

        DB::transaction(function () use ($tenantId, $year, $dryRun) {
            $fiscalYear = FiscalYear::where('tenant_id', $tenantId)
                ->where('year', $year)
                ->firstOrFail();

            $endDate = Carbon::parse($fiscalYear->end_date);

            $this->info("Starte Abschluss für {$year}..." . ($dryRun ? " (DRY-RUN)" : ""));

            // --- Konten vorbereiten ---
            $guvAccount = Account::firstOrCreate(
                ['tenant_id' => $tenantId, 'number' => '8020'],
                ['name' => 'GuV-Konto', 'type' => 'equity']
            );

            $equityAccount = Account::firstOrCreate(
                ['tenant_id' => $tenantId, 'number' => '0860'],
                ['name' => 'Eigenkapital', 'type' => 'equity']
            );

            $steuerVerrechnung = Account::firstOrCreate(
                ['tenant_id' => $tenantId, 'number' => '1790'],
                ['name' => 'USt-Verrechnung', 'type' => 'liability']
            );

            // --- Summen berechnen ---
            $revenue = Entry::where('tenant_id', $tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->whereHas('creditAccount', fn($q) => $q->where('type', 'revenue'))
                ->sum('amount');

            $expenses = Entry::where('tenant_id', $tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->whereHas('debitAccount', fn($q) => $q->where('type', 'expense'))
                ->sum('amount');

            $result = $revenue - $expenses;

            $this->line("  Erträge:  " . number_format($revenue, 2, ',', '.') . " €");
            $this->line("  Aufwände: " . number_format($expenses, 2, ',', '.') . " €");
            $this->line("  Ergebnis: " . number_format($result, 2, ',', '.') . " €");

            // --- Dry Run Check ---
            if ($dryRun) {
                $this->warn("DRY-RUN: Es werden keine Buchungen gespeichert.");
                return;
            }

            /**
             * 1. Erfolgskonten -> GuV
             */
            $revenueEntries = Entry::with('creditAccount')
                ->where('tenant_id', $tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->whereHas('creditAccount', fn($q) => $q->where('type', 'revenue'))
                ->get();

            foreach ($revenueEntries as $entry) {
                Entry::create([
                    'tenant_id'        => $tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $endDate,
                    'debit_account_id' => $entry->credit_account_id,
                    'credit_account_id'=> $guvAccount->id,
                    'amount'           => $entry->amount,
                    'description'      => "Abschluss Ertrag {$year}",
                    'transaction_id'   => Str::uuid(),
                ]);
            }

            $expenseEntries = Entry::with('debitAccount')
                ->where('tenant_id', $tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->whereHas('debitAccount', fn($q) => $q->where('type', 'expense'))
                ->get();

            foreach ($expenseEntries as $entry) {
                Entry::create([
                    'tenant_id'        => $tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $endDate,
                    'debit_account_id' => $guvAccount->id,
                    'credit_account_id'=> $entry->debit_account_id,
                    'amount'           => $entry->amount,
                    'description'      => "Abschluss Aufwand {$year}",
                    'transaction_id'   => Str::uuid(),
                ]);
            }

            /**
             * 2. GuV-Saldo -> Eigenkapital
             */
            if ($result > 0) {
                Entry::create([
                    'tenant_id'        => $tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $endDate,
                    'debit_account_id' => $guvAccount->id,
                    'credit_account_id'=> $equityAccount->id,
                    'amount'           => $result,
                    'description'      => "Jahresabschluss {$year} | Gewinn",
                    'transaction_id'   => Str::uuid(),
                ]);
            } elseif ($result < 0) {
                Entry::create([
                    'tenant_id'        => $tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $endDate,
                    'debit_account_id' => $equityAccount->id,
                    'credit_account_id'=> $guvAccount->id,
                    'amount'           => abs($result),
                    'description'      => "Jahresabschluss {$year} | Verlust",
                    'transaction_id'   => Str::uuid(),
                ]);
            }

            /**
             * 3. Umsatzsteuer / Vorsteuer -> 1790
             */
            $ustKonto = 1776;
            $vstKonto = 1576;

            $ustSaldo = Entry::where('tenant_id', $tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->sum(DB::raw("
                    CASE
                        WHEN debit_account_id = {$ustKonto} THEN amount
                        WHEN credit_account_id = {$ustKonto} THEN -amount
                        ELSE 0
                    END
                "));

            $vstSaldo = Entry::where('tenant_id', $tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->sum(DB::raw("
                    CASE
                        WHEN debit_account_id = {$vstKonto} THEN amount
                        WHEN credit_account_id = {$vstKonto} THEN -amount
                        ELSE 0
                    END
                "));

            if ($ustSaldo != 0) {
                Entry::create([
                    'tenant_id'        => $tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $endDate,
                    'debit_account_id' => $ustSaldo > 0 ? $steuerVerrechnung->id : $ustKonto,
                    'credit_account_id'=> $ustSaldo > 0 ? $ustKonto : $steuerVerrechnung->id,
                    'amount'           => abs($ustSaldo),
                    'description'      => "Abschluss Umsatzsteuer {$year}",
                    'transaction_id'   => Str::uuid(),
                ]);
            }

            if ($vstSaldo != 0) {
                Entry::create([
                    'tenant_id'        => $tenantId,
                    'fiscal_year_id'   => $fiscalYear->id,
                    'booking_date'     => $endDate,
                    'debit_account_id' => $vstSaldo > 0 ? $vstKonto : $steuerVerrechnung->id,
                    'credit_account_id'=> $vstSaldo > 0 ? $steuerVerrechnung->id : $vstKonto,
                    'amount'           => abs($vstSaldo),
                    'description'      => "Abschluss Vorsteuer {$year}",
                    'transaction_id'   => Str::uuid(),
                ]);
            }

            $fiscalYear->update(['closed' => true]);
            $this->info("Jahr {$year} abgeschlossen.");

            /**
             * 4. Neues Jahr eröffnen (EBK)
             */
            $nextYear = $year + 1;
            $newFiscalYear = FiscalYear::create([
                'tenant_id' => $tenantId,
                'year' => $nextYear,
                'start_date' => "{$nextYear}-01-01",
                'end_date' => "{$nextYear}-12-31",
                'is_current' => true,
                'closed' => false,
            ]);

            $this->info("Eröffne neues Jahr {$nextYear}...");

            $ebk = Account::firstOrCreate(
                ['tenant_id' => $tenantId, 'number' => '9000'],
                ['name' => 'Eröffnungsbilanzkonto', 'type' => 'equity']
            );

            $entries = Entry::where('tenant_id', $tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->get();

            $balances = [];
            foreach ($entries as $entry) {
                if ($entry->debit_account_id) {
                    $balances[$entry->debit_account_id] = ($balances[$entry->debit_account_id] ?? 0) + $entry->amount;
                }
                if ($entry->credit_account_id) {
                    $balances[$entry->credit_account_id] = ($balances[$entry->credit_account_id] ?? 0) - $entry->amount;
                }
            }

            foreach ($balances as $accountId => $saldo) {
                if (abs($saldo) < 0.01) continue;

                $account = Account::find($accountId);
                if (!$account) continue;

                // Nur Bestandskonten vortragen
                if (!in_array($account->type, ['asset','liability','equity'])) continue;

                if ($saldo > 0) {
                    Entry::create([
                        'tenant_id'        => $tenantId,
                        'fiscal_year_id'   => $newFiscalYear->id,
                        'booking_date'     => "{$nextYear}-01-01",
                        'debit_account_id' => $accountId,
                        'credit_account_id'=> $ebk->id,
                        'amount'           => $saldo,
                        'description'      => "EB-Saldo {$nextYear}",
                        'transaction_id'   => Str::uuid(),
                    ]);
                } else {
                    Entry::create([
                        'tenant_id'        => $tenantId,
                        'fiscal_year_id'   => $newFiscalYear->id,
                        'booking_date'     => "{$nextYear}-01-01",
                        'debit_account_id' => $ebk->id,
                        'credit_account_id'=> $accountId,
                        'amount'           => abs($saldo),
                        'description'      => "EB-Saldo {$nextYear}",
                        'transaction_id'   => Str::uuid(),
                    ]);
                }
            }

            /**
             * 5. Kontrolle: EBK-Summe = 0
             */
            $ebkSaldo = Entry::where('tenant_id', $tenantId)
                ->where('fiscal_year_id', $newFiscalYear->id)
                ->sum(DB::raw("
                    CASE
                        WHEN debit_account_id = {$ebk->id} THEN amount
                        WHEN credit_account_id = {$ebk->id} THEN -amount
                        ELSE 0
                    END
                "));

            if (abs($ebkSaldo) < 0.01) {
                $this->info("✅ EBK ist ausgeglichen.");
            } else {
                $this->error("❌ EBK-Differenz: " . number_format($ebkSaldo, 2, ',', '.') . " €");
            }

            $this->info("Eröffnungsbuchungen für {$nextYear} erstellt.");
        });
    }
}
