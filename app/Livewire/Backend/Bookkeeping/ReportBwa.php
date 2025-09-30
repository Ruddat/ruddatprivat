<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Entry;
use App\Models\FiscalYear;
use App\Models\Tenant;
use App\Models\BwaGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReportBwa extends Component
{
    public $tenantId;
    public $availableTenants;

    public function mount()
    {
        // Alle Mandanten des Kunden holen
        $this->availableTenants = Tenant::where('customer_id', Auth::guard('customer')->id())
            ->orderBy('name')
            ->get();

        // Aktuellen Mandanten finden oder ersten nehmen
        $currentTenant = $this->availableTenants->where('is_current', true)->first();
        $firstTenant = $this->availableTenants->first();
        
        $this->tenantId = $currentTenant ? $currentTenant->id : ($firstTenant ? $firstTenant->id : null);
    }

    public function updatedTenantId($value)
    {
        // Wenn Mandant gewechselt wird, Report aktualisieren
        // $this->render() wird automatisch aufgerufen
    }

    public function render()
    {
        if (! $this->tenantId || !$this->availableTenants->count()) {
            return $this->view([
                'rows' => [], 
                'totalRevenue' => 0, 
                'result' => 0, 
                'hasData' => false,
                'availableTenants' => $this->availableTenants,
                'entries' => collect(),
                'fiscalYear' => null,
                'tenant' => null
            ]);
        }

        $tenant = Tenant::find($this->tenantId);
        $skr = $tenant?->chart_of_accounts ?? 'basic';

        $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
            ->where('is_current', true)
            ->first();

        if (! $fiscalYear) {
            return $this->view([
                'rows' => [], 
                'totalRevenue' => 0, 
                'result' => 0, 
                'hasData' => false,
                'availableTenants' => $this->availableTenants,
                'tenant' => $tenant,
                'entries' => collect(),
                'fiscalYear' => null
            ]);
        }

        // Buchungen laden
        $entries = Entry::with(['debitAccount', 'creditAccount'])
            ->where('tenant_id', $this->tenantId)
            ->where('fiscal_year_id', $fiscalYear->id)
            ->get();

        if ($entries->isEmpty()) {
            return $this->view([
                'rows' => [], 
                'totalRevenue' => 0, 
                'result' => 0, 
                'hasData' => false,
                'availableTenants' => $this->availableTenants,
                'tenant' => $tenant,
                'fiscalYear' => $fiscalYear,
                'entries' => $entries
            ]);
        }

        // BWA-Gruppen laden
        $groups = BwaGroup::where('skr', $skr)
            ->orderBy('order')
            ->get();

        if ($groups->isEmpty()) {
            $groups = BwaGroup::where('skr', 'basic')
                ->orderBy('order')
                ->get();
        }

        // Debug: Zeige geladene Gruppen
        logger("=== BWA GRUPPEN ===");
        logger("SKR: " . $skr);
        foreach ($groups as $g) {
            logger("Gruppe: {$g->group_label} ({$g->account_number_from}-{$g->account_number_to}) - Order: {$g->order}");
        }

        $rows = [];
        foreach ($groups as $g) {
            // Verhindere doppelte Gruppen-Keys
            $key = $g->group_key;
            if (!isset($rows[$key])) {
                $rows[$key] = [
                    'label' => $g->group_label,
                    'amount' => 0,
                    'order' => $g->order,
                ];
            }
        }

        // Beträge auf Gruppen verteilen - VERBESSERTE VERSION
        logger("=== BWA ZUORDNUNG ===");
        foreach ($entries as $e) {
            // Prüfe beide Konten der Buchung
            foreach (['debitAccount', 'creditAccount'] as $accountSide) {
                $account = $e->$accountSide;
                if (!$account) continue;

                // Nur Revenue und Expense Konten berücksichtigen
                if (!in_array($account->type, ['revenue', 'expense'])) {
                    continue;
                }

                // Betrag und Richtung bestimmen
                $amount = $e->amount;
                
                if ($account->type === 'revenue') {
                    // Revenue: Im Haben positiv, im Soll negativ
                    $amount = ($accountSide === 'creditAccount') ? $amount : -$amount;
                } elseif ($account->type === 'expense') {
                    // Expense: Im Soll positiv, im Haben negativ
                    $amount = ($accountSide === 'debitAccount') ? $amount : -$amount;
                }

                // Passende Gruppe finden
                $matchingGroups = $groups->filter(function ($g) use ($account) {
                    $inRange = $account->number >= $g->account_number_from
                            && $account->number <= $g->account_number_to;
                    return $inRange;
                });

                if ($matchingGroups->isNotEmpty()) {
                    // Engste Range wählen (kleinster Bereich)
                    $group = $matchingGroups->sortBy(function ($g) use ($account) {
                        $rangeSize = $g->account_number_to - $g->account_number_from;
                        // Priorisiere spezifischere Bereiche
                        return $rangeSize;
                    })->first();

                    // Debug für Revenue
                    if ($account->type === 'revenue') {
                        logger("REVENUE: Konto {$account->number} -> Gruppe {$group->group_key}: {$amount} €");
                    }

                    if (isset($rows[$group->group_key])) {
                        $rows[$group->group_key]['amount'] += $amount;
                    }
                } else {
                    // Debug: Keine Gruppe gefunden
                    if ($account->type === 'revenue') {
                        logger("KEINE GRUPPE für Revenue-Konto {$account->number}!");
                    }
                }
            }
        }

        // Berechnungen
        $totalRevenue = $rows['revenue']['amount'] ?? 0;
        
        // Alle Expense-Gruppen sammeln (alles außer revenue)
        $expenseRows = array_filter($rows, function($row, $key) {
            return $key !== 'revenue' && $row['amount'] != 0;
        }, ARRAY_FILTER_USE_BOTH);

        $totalExpenses = array_sum(array_column($expenseRows, 'amount'));
        $result = $totalRevenue - $totalExpenses;

        // Debug: Zeige Endergebnis
        logger("=== BWA ERGEBNIS ===");
        logger("Total Revenue: " . ($rows['revenue']['amount'] ?? 0));
        logger("Total Expenses: " . $totalExpenses);
        logger("Result: " . $result);
        foreach ($rows as $key => $row) {
            if ($row['amount'] != 0) {
                logger("{$row['label']}: {$row['amount']} €");
            }
        }

        // Nach Order sortieren
        uasort($rows, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        return $this->view([
            'rows' => $rows,
            'totalRevenue' => $totalRevenue,
            'result' => $result,
            'fiscalYear' => $fiscalYear,
            'hasData' => true,
            'availableTenants' => $this->availableTenants,
            'tenant' => $tenant,
            'entries' => $entries
        ]);
    }

    protected function view(array $data)
    {
        return view('livewire.backend.bookkeeping.report-bwa', $data)
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}