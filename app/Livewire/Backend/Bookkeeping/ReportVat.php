<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Entry;
use Livewire\Component;

class ReportVat extends Component
{
    public $tenantId = 1;

    public function render()
    {
        $ust = Entry::with('creditAccount')
            ->where('tenant_id', $this->tenantId)
            ->whereHas('creditAccount', fn ($q) => $q->where('number', '1776'))
            ->sum('amount');

        $vorsteuer = Entry::with('debitAccount')
            ->where('tenant_id', $this->tenantId)
            ->whereHas('debitAccount', fn ($q) => $q->where('number', '1576'))
            ->sum('amount');

        $zahllast = $ust - $vorsteuer;

        return view('livewire.backend.bookkeeping.report-vat', compact('ust', 'vorsteuer', 'zahllast'))
            ->extends('backend.layouts.backend');
    }
}
