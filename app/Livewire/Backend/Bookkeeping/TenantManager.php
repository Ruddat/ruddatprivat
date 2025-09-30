<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Tenant;
use App\Services\ChartOfAccountsService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TenantManager extends Component
{
    public $tenants;
    public $showForm = false; // Neue Property für Formular-Sichtbarkeit

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
    public $chart_of_accounts = 'basic';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . $this->tenantId,
            'phone' => 'nullable|string|max:20',
            'street' => 'required|string|max:255',
            'house_number' => 'required|string|max:20',
            'zip' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'vat_id' => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:100',
            'court_register' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'bic' => 'nullable|string|max:11',
            'fiscal_year_start' => 'required|date',
            'currency' => 'required|string|max:3',
            'active' => 'boolean',
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
        $this->tenants = Tenant::where('customer_id', Auth::guard('customer')->id())
            ->orderBy('name')
            ->get();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $tenant = Tenant::where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($id);

        $this->tenantId = $tenant->id;
        $this->fill($tenant->toArray());
        $this->showForm = true;
    }

    public function save()
    {
        $data = $this->validate();

        $data['customer_id'] = Auth::guard('customer')->id();

        if ($this->tenantId) {
            $tenant = Tenant::where('customer_id', $data['customer_id'])
                ->findOrFail($this->tenantId);

            $tenant->update($data);
            session()->flash('success', 'Mandant aktualisiert!');
        } else {
            $tenant = Tenant::create($data);
            session()->flash('success', 'Mandant angelegt!');
        }

        // Kontorahmen erzeugen
        ChartOfAccountsService::createForTenant($tenant->id, $this->chart_of_accounts);

        $this->resetForm();
        $this->loadTenants();
        $this->showForm = false;
    }

    public function setCurrent($id)
    {
        $customerId = Auth::guard('customer')->id();

        // nur Tenants dieses Customers zurücksetzen
        Tenant::where('customer_id', $customerId)->update(['is_current' => false]);

        // neuen setzen
        $tenant = Tenant::where('customer_id', $customerId)->findOrFail($id);
        $tenant->update(['is_current' => true]);

        session()->flash('success', "Mandant {$tenant->name} ist jetzt aktiv!");
        $this->loadTenants();
    }

    public function resetForm()
    {
        $this->reset([
            'tenantId', 'name', 'slug', 'email', 'phone', 'street', 'house_number',
            'zip', 'city', 'country', 'tax_number', 'vat_id', 'commercial_register',
            'court_register', 'bank_name', 'iban', 'bic', 'fiscal_year_start',
            'currency', 'active', 'chart_of_accounts'
        ]);

        $this->country = 'Deutschland';
        $this->fiscal_year_start = '2025-01-01';
        $this->currency = 'EUR';
        $this->active = true;
        $this->chart_of_accounts = 'basic';
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.backend.bookkeeping.tenant-manager')
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
