<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Tenant;
use App\Services\ChartOfAccountsService;
use Livewire\Component;

class TenantManager extends Component
{
    public $tenants;

    public $tenantId = null;

    // Formularfelder
    public $name;

    public $slug;

    public $email;

    public $phone;

    public $street;

    public $house_number;

    public $zip;

    public $city;

    public $country = 'Deutschland';

    public $tax_number;

    public $vat_id;

    public $commercial_register;

    public $court_register;

    public $bank_name;

    public $iban;

    public $bic;

    public $fiscal_year_start = '2025-01-01';

    public $currency = 'EUR';

    public $active = true;

    public $chart_of_accounts = 'basic'; // Auswahl im Formular

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . $this->tenantId,
            'email' => 'nullable|email',
            'iban' => 'nullable|string|max:34',
            'chart_of_accounts' => 'required|string',
        ];
    }

    public function mount()
    {
        $this->loadTenants();
    }

    public function loadTenants()
    {
        $this->tenants = Tenant::orderBy('name')->get();
    }

    public function edit($id)
    {
        $tenant = Tenant::findOrFail($id);
        $this->tenantId = $tenant->id;

        $this->fill($tenant->toArray());
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->tenantId) {
            $tenant = Tenant::findOrFail($this->tenantId);
            $tenant->update($data);
            session()->flash('success', 'Tenant aktualisiert!');
        } else {
            Tenant::create($data);
            session()->flash('success', 'Tenant angelegt!');
        }

        // Kontorahmen erzeugen
        ChartOfAccountsService::createForTenant($tenant->id, $this->chart_of_accounts);

        $this->resetForm();
        $this->loadTenants();
    }

    public function setCurrent($id)
    {
        // alle zurücksetzen
        Tenant::query()->update(['is_current' => false]);

        // neuen setzen
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['is_current' => true]);

        session()->flash('success', "Tenant {$tenant->name} ist jetzt aktiv!");
        $this->loadTenants();
    }

    public function resetForm()
    {
        $this->reset([
            'tenantId', 'name', 'slug', 'email', 'phone', 'street', 'house_number',
            'zip', 'city', 'country', 'tax_number', 'vat_id', 'commercial_register',
            'court_register', 'bank_name', 'iban', 'bic', 'fiscal_year_start',
            'currency', 'active',
        ]);
        $this->country = 'Deutschland';
        $this->fiscal_year_start = '2025-01-01';
        $this->currency = 'EUR';
        $this->active = true;
    }

    public function render()
    {
        return view('livewire.backend.bookkeeping.tenant-manager')
            ->extends('backend.layouts.backend');
    }
}
