<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Entry;
use App\Models\FiscalYear;
use App\Models\Tenant;
use Livewire\Component;

class ReportProfitLoss extends Component
{
    public $tenantId;

    public function mount()
    {
        $this->tenantId = Tenant::current()?->id ?? null;
    }

    public function render()
    {
        if (! $this->tenantId) {
            return view('livewire.backend.bookkeeping.report-profit-loss', [
                'revenue' => 0,
                'expenses' => 0,
                'profit' => 0,
                'fiscalYear' => null,
                'tenant' => null,
            ])->extends('backend.layouts.backend');
        }

        // aktuelles Jahr ermitteln
        $fiscalYear = FiscalYear::where('tenant_id', $this->tenantId)
            ->where('is_current', true)
            ->first();

        if (! $fiscalYear) {
            return view('livewire.backend.bookkeeping.report-profit-loss', [
                'revenue' => 0,
                'expenses' => 0,
                'profit' => 0,
                'fiscalYear' => null,
                'tenant' => Tenant::find($this->tenantId),
            ])->extends('backend.layouts.backend');
        }

        // Alle Buchungen für dieses Jahr laden
        $entries = Entry::with(['debitAccount', 'creditAccount'])
            ->where('tenant_id', $this->tenantId)
            ->where('fiscal_year_id', $fiscalYear->id)
            ->get();

        $revenue = 0;
        $expenses = 0;

        foreach ($entries as $e) {
            if (($e->creditAccount && $e->creditAccount->type === 'revenue') ||
                ($e->debitAccount && $e->debitAccount->type === 'revenue')) {
                $revenue += $e->amount;
            }

            if (($e->debitAccount && $e->debitAccount->type === 'expense') ||
                ($e->creditAccount && $e->creditAccount->type === 'expense')) {
                $expenses += $e->amount;
            }
        }

        $profit = $revenue - $expenses;

        return view('livewire.backend.bookkeeping.report-profit-loss', [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $profit,
            'fiscalYear' => $fiscalYear,
            'tenant' => Tenant::find($this->tenantId),
        ])->extends('backend.layouts.backend');
    }
}
