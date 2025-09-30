<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Entry;
use App\Models\Tenant;
use App\Models\FiscalYear;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReportProfitLoss extends Component
{
    public $tenantId;
    public $yearId; // ausgewähltes Geschäftsjahr

    public function mount()
    {
        $this->tenantId = Tenant::where('customer_id', Auth::guard('customer')->id())
            ->where('is_current', true)
            ->value('id');

        if ($this->tenantId) {
            $currentYear = FiscalYear::where('tenant_id', $this->tenantId)
                ->where('is_current', true)
                ->first();
            $this->yearId = $currentYear?->id;
        }
    }

    protected function view(array $data)
    {
        return view('livewire.backend.bookkeeping.report-profit-loss', $data)
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }

    public function render()
    {
        if (! $this->tenantId) {
            return $this->view([
                'revenue'         => 0,
                'expenses'        => 0,
                'profit'          => 0,
                'fiscalYear'      => null,
                'tenant'          => null,
                'years'           => collect(),
                'yearlySummaries' => collect(),
            ]);
        }

        $tenant = Tenant::find($this->tenantId);
        $years  = FiscalYear::where('tenant_id', $this->tenantId)->orderBy('year', 'desc')->get();

        $fiscalYear = $this->yearId
            ? $years->firstWhere('id', $this->yearId)
            : null;

        $revenue  = 0;
        $expenses = 0;
        $profit   = 0;

        if ($fiscalYear) {
            $entries = Entry::with(['debitAccount', 'creditAccount'])
                ->where('tenant_id', $this->tenantId)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->get();

            foreach ($entries as $e) {
                if ($e->creditAccount && $e->creditAccount->type === 'revenue') {
                    $revenue += $e->amount;
                }
                if ($e->debitAccount && $e->debitAccount->type === 'expense') {
                    $expenses += $e->amount;
                }
            }

            $profit = $revenue - $expenses;
        }

        // Summenübersicht pro Jahr
        $yearlySummaries = collect();
        foreach ($years as $year) {
            $rev = 0;
            $exp = 0;

            $entries = Entry::with(['debitAccount', 'creditAccount'])
                ->where('tenant_id', $this->tenantId)
                ->where('fiscal_year_id', $year->id)
                ->get();

            foreach ($entries as $e) {
                if ($e->creditAccount && $e->creditAccount->type === 'revenue') {
                    $rev += $e->amount;
                }
                if ($e->debitAccount && $e->debitAccount->type === 'expense') {
                    $exp += $e->amount;
                }
            }

            $yearlySummaries->push([
                'year'    => $year,
                'revenue' => $rev,
                'expenses'=> $exp,
                'profit'  => $rev - $exp,
            ]);
        }

        return $this->view([
            'revenue'         => $revenue,
            'expenses'        => $expenses,
            'profit'          => $profit,
            'fiscalYear'      => $fiscalYear,
            'tenant'          => $tenant,
            'years'           => $years,
            'yearlySummaries' => $yearlySummaries,
        ]);
    }
}
