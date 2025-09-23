<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Entry;
use Livewire\Component;

class EntryList extends Component
{
    public $tenantId = 1;

    public function render()
    {
        $entries = Entry::with(['debitAccount', 'creditAccount'])
            ->where('tenant_id', $this->tenantId)
            ->orderBy('booking_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();

        // Summen berechnen
        $totalDebit = $entries->sum('amount');
        $totalCredit = $entries->sum('amount'); // gleiche Werte, da doppelte Buchführung

        return view('livewire.backend.bookkeeping.entry-list', compact('entries', 'totalDebit', 'totalCredit'))
            ->extends('backend.layouts.backend');
    }
}
