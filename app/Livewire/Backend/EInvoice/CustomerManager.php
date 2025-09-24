<?php

namespace App\Livewire\Backend\EInvoice;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;
use App\Models\ModInvoiceRecipient; // 👈 wichtig

class CustomerManager extends Component
{
    use WithPagination;

    public $newCustomer = [];
    public $editCustomerId = null;
    public $showForm = false;
    public $isEditMode = false;
    public $search = '';
    public $filterActive = true;

    protected $rules = [
        'newCustomer.name' => 'required|string|max:255',
        'newCustomer.email' => 'required|email|unique:mod_invoice_recipients,email',
        'newCustomer.phone' => 'nullable|string|max:255',
        'newCustomer.address' => 'nullable|string|max:255',
        'newCustomer.city' => 'nullable|string|max:255',
        'newCustomer.zip_code' => 'nullable|string|max:255',
        'newCustomer.country' => 'nullable|string|max:255',
        'newCustomer.customer_type' => 'required|in:private,business',
        'newCustomer.company_name' => 'nullable|string|max:255',
        'newCustomer.vat_number' => 'nullable|string|max:255',
        'newCustomer.payment_terms' => 'nullable|string|max:255',
        'newCustomer.notes' => 'nullable|string',
        'newCustomer.is_active' => 'boolean',
        'newCustomer.default_currency' => 'required|string|max:3',
        'newCustomer.e_invoice_format' => 'nullable|string|max:255',
        'newCustomer.newsletter_opt_in' => 'boolean',
    ];

    public function mount()
    {
        $this->resetCustomerForm();
    }

    public function resetCustomerForm()
    {
        $this->newCustomer = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'city' => '',
            'zip_code' => '',
            'country' => 'Germany',
            'customer_type' => 'private',
            'company_name' => '',
            'vat_number' => '',
            'payment_terms' => '14 days',
            'notes' => '',
            'default_currency' => 'EUR',
            'e_invoice_format' => null,
            'newsletter_opt_in' => 0,
            'is_active' => true,
        ];
        $this->editCustomerId = null;
        $this->isEditMode = false;
        $this->showForm = false;
    }

    public function cancelEdit()
    {
        $this->resetCustomerForm();
    }

    public function createCustomer()
    {
        $this->validate();
        $customerId = Auth::guard('customer')->id();

        ModInvoiceRecipient::create(array_merge(
            $this->newCustomer,
            ['customer_id' => $customerId]
        ));

        $this->resetCustomerForm();
        session()->flash('message', 'Kunde erfolgreich erstellt!');
    }

    public function startEditCustomer($id)
    {
        $customerId = Auth::guard('customer')->id();

        $customer = ModInvoiceRecipient::where('id', $id)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $this->newCustomer = $customer->only([
            'name', 'email', 'phone', 'address', 'city', 'zip_code',
            'country', 'customer_type', 'company_name', 'vat_number',
            'payment_terms', 'notes', 'default_currency',
            'e_invoice_format', 'newsletter_opt_in', 'is_active'
        ]);

        $this->editCustomerId = $id;
        $this->isEditMode = true;
        $this->showForm = true;
    }

    public function updateCustomer()
    {
        $this->validate([
            'newCustomer.name' => 'required|string|max:255',
            'newCustomer.email' => 'required|email|unique:mod_invoice_recipients,email,' . $this->editCustomerId,
        ]);

        $customer = ModInvoiceRecipient::findOrFail($this->editCustomerId);
        $customer->update($this->newCustomer);

        $this->resetCustomerForm();
        session()->flash('message', 'Kunde erfolgreich aktualisiert!');
    }

    public function deleteCustomer($id)
    {
        $customerId = Auth::guard('customer')->id();

        $customer = ModInvoiceRecipient::where('id', $id)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $customer->delete();
        session()->flash('message', 'Kunde erfolgreich gelöscht!');
    }

    public function toggleActive($id)
    {
        $customer = ModInvoiceRecipient::findOrFail($id);
        $customer->is_active = !$customer->is_active;
        $customer->save();
    }

    public function toggleFilter()
    {
        $this->filterActive = !$this->filterActive;
    }

    public function exportCustomers()
    {
        return Excel::download(new CustomersExport($this->search, $this->filterActive), 'customers.xlsx');
    }

    public function getCustomersProperty()
    {
        $customerId = Auth::guard('customer')->id();

        $query = ModInvoiceRecipient::where('customer_id', $customerId);

        if ($this->filterActive) {
            $query->where('is_active', true);
        }

        if ($this->search) {
            $query->where(function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function openCreateForm()
    {
        $this->resetCustomerForm();
        $this->showForm = true;
        $this->isEditMode = false;
    }

    public function render()
    {
        return view('livewire.backend.e-invoice.customer-manager', [
            'customers' => $this->customers,
        ])->extends('backend.customer.layouts.app')
          ->section('content');
    }
}
