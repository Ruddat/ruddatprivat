<?php

namespace App\Livewire\Backend;

use App\Models\Entry;
use Livewire\Component;

class Dashboard extends Component
{
    public $tenantId = 1;

    public function render()
    {
        // Gewinn berechnen
        $revenues = Entry::where('tenant_id', $this->tenantId)
            ->whereHas('creditAccount', fn ($q) => $q->where('type', 'revenue'))
            ->sum('amount');

        $expenses = Entry::where('tenant_id', $this->tenantId)
            ->whereHas('debitAccount', fn ($q) => $q->where('type', 'expense'))
            ->sum('amount');

        $profit = $revenues - $expenses;

        // Umsatzsteuer
        $ust = Entry::where('tenant_id', $this->tenantId)
            ->whereHas('creditAccount', fn ($q) => $q->where('number', '1776'))
            ->sum('amount');

        $vorsteuer = Entry::where('tenant_id', $this->tenantId)
            ->whereHas('debitAccount', fn ($q) => $q->where('number', '1576'))
            ->sum('amount');

        $tax_liability = $ust - $vorsteuer;

        // Fixkosten (PayPal, Lizenz, Hosting, Kontoführung)
        $fixcostAccounts = ['4975', '4905', '4950', '4970'];
        $fixcosts = Entry::where('tenant_id', $this->tenantId)
            ->whereHas('debitAccount', fn ($q) => $q->whereIn('number', $fixcostAccounts))
            ->sum('amount');

        return view('livewire.backend.dashboard', compact(
            'revenues', 'expenses', 'profit', 'ust', 'vorsteuer', 'tax_liability', 'fixcosts',
        ))->extends('backend.layouts.backend');
    }
}
