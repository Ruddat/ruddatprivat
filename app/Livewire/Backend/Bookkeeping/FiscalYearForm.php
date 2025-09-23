<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\FiscalYear;
use App\Models\Tenant;
use Illuminate\Validation\Rule;
use Livewire\Component;

class FiscalYearForm extends Component
{
    public $tenantId;

    public $year;

    public $start_date;

    public $end_date;

    public $closed = false;

    public function mount()
    {
        $this->tenantId = Tenant::current()?->id ?? 1; // Fallback
    }

    public function save()
    {
        $this->validate([
            'year' => [
                'required', 'integer',
                Rule::unique('fiscal_years')->where(fn ($q) => $q->where('tenant_id', $this->tenantId)),
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        FiscalYear::create([
            'tenant_id' => $this->tenantId,
            'year' => $this->year,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'closed' => $this->closed,
            'is_current' => false,
        ]);

        session()->flash('success', "Buchungsjahr {$this->year} angelegt!");
        $this->reset(['year', 'start_date', 'end_date', 'closed']);
    }

    public function setCurrent($id)
    {
        FiscalYear::where('tenant_id', $this->tenantId)->update(['is_current' => false]);

        $year = FiscalYear::where('tenant_id', $this->tenantId)->findOrFail($id);
        $year->update(['is_current' => true]);

        session()->flash('success', "Buchungsjahr {$year->year} ist jetzt aktiv!");
    }

    public function toggleClosed($id)
    {
        $year = FiscalYear::where('tenant_id', $this->tenantId)->findOrFail($id);
        $year->update(['closed' => ! $year->closed]);

        session()->flash('success', "Buchungsjahr {$year->year} wurde " . ($year->closed ? 'geschlossen' : 'geöffnet') . '.');
    }

    public function render()
    {
        $years = FiscalYear::where('tenant_id', $this->tenantId)
            ->orderByDesc('year')
            ->get();

        return view('livewire.backend.bookkeeping.fiscal-year-form', compact('years'))
            ->extends('backend.layouts.backend');
    }
}
