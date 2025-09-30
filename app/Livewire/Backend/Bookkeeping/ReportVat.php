<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Entry;
use App\Models\FiscalYear;
use App\Models\Tenant;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ReportVat extends Component
{
    public $tenantId;
    public $yearId; // ausgewähltes Jahr

    public function mount()
    {
        $this->tenantId = Tenant::where('customer_id', Auth::guard('customer')->id())
            ->where('is_current', true)
            ->value('id');

        // aktuelles Geschäftsjahr vorselektieren
        $this->yearId = FiscalYear::where('tenant_id', $this->tenantId)
            ->where('is_current', true)
            ->value('id');
    }

    public function render()
    {
        $years = FiscalYear::where('tenant_id', $this->tenantId)
            ->orderBy('year', 'desc')
            ->get();

        $ust = $vorsteuer = 0;

        if ($this->yearId) {
            $ust = Entry::with('creditAccount')
                ->where('tenant_id', $this->tenantId)
                ->where('fiscal_year_id', $this->yearId)
                ->whereHas('creditAccount', fn ($q) => $q->where('number', '1776'))
                ->sum('amount');

            $vorsteuer = Entry::with('debitAccount')
                ->where('tenant_id', $this->tenantId)
                ->where('fiscal_year_id', $this->yearId)
                ->whereHas('debitAccount', fn ($q) => $q->where('number', '1576'))
                ->sum('amount');
        }

        $zahllast = $ust - $vorsteuer;

        return view('livewire.backend.bookkeeping.report-vat', [
            'years' => $years,
            'ust' => $ust,
            'vorsteuer' => $vorsteuer,
            'zahllast' => $zahllast,
        ])
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
