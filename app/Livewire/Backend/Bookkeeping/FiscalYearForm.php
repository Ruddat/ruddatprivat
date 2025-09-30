<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\FiscalYear;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class FiscalYearForm extends Component
{
    public $tenantId;
    public $availableTenants;
    
    public $year;
    public $start_date;
    public $end_date;
    public $closed = false;

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
        // Formular zurücksetzen wenn Mandant gewechselt wird
        $this->reset(['year', 'start_date', 'end_date', 'closed']);
    }

    public function save()
    {
        $this->validate([
            'tenantId' => 'required|exists:tenants,id',
            'year' => [
                'required', 'integer', 'min:2000', 'max:2100',
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

        session()->flash('success', "Buchungsjahr {$this->year} für " . $this->getCurrentTenantName() . " angelegt!");
        $this->reset(['year', 'start_date', 'end_date', 'closed']);
    }

    public function setCurrent($id)
    {
        $year = FiscalYear::where('tenant_id', $this->tenantId)->findOrFail($id);
        
        // Alle Jahre des Mandanten auf nicht-aktuell setzen
        FiscalYear::where('tenant_id', $this->tenantId)->update(['is_current' => false]);
        
        // Gewähltes Jahr auf aktuell setzen
        $year->update(['is_current' => true]);

        session()->flash('success', "Buchungsjahr {$year->year} ist jetzt aktiv für " . $this->getCurrentTenantName() . "!");
    }

    public function toggleClosed($id)
    {
        $year = FiscalYear::where('tenant_id', $this->tenantId)->findOrFail($id);
        $year->update(['closed' => ! $year->closed]);

        $action = $year->closed ? 'geschlossen' : 'geöffnet';
        session()->flash('success', "Buchungsjahr {$year->year} wurde {$action}.");
    }

    public function deleteYear($id)
    {
        $year = FiscalYear::where('tenant_id', $this->tenantId)->findOrFail($id);
        
        // Prüfen ob Buchungen existieren
        if ($year->entries()->exists()) {
            session()->flash('error', "Buchungsjahr {$year->year} kann nicht gelöscht werden, da bereits Buchungen existieren!");
            return;
        }

        $year->delete();
        session()->flash('success', "Buchungsjahr {$year->year} wurde gelöscht!");
    }

    private function getCurrentTenantName()
    {
        return $this->availableTenants->firstWhere('id', $this->tenantId)->name ?? 'Unbekannt';
    }

    public function render()
    {
        $years = collect();
        $currentTenant = null;

        if ($this->tenantId) {
            $years = FiscalYear::where('tenant_id', $this->tenantId)
                ->orderByDesc('year')
                ->get();
                
            $currentTenant = $this->availableTenants->firstWhere('id', $this->tenantId);
        }

        return view('livewire.backend.bookkeeping.fiscal-year-form', compact('years', 'currentTenant'))
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}