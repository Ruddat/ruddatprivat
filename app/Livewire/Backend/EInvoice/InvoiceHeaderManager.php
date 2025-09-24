<?php

namespace App\Livewire\Backend\EInvoice;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ModInvoiceCreator;
use Illuminate\Support\Facades\Auth;

class InvoiceHeaderManager extends Component
{
    use WithFileUploads;

    public $invoiceHeaders;
    public $invoiceHeader = [];
    public $showForm = false;
    public $logo; // Für den Logo-Upload

    protected $rules = [
        'invoiceHeader.first_name' => 'required|string|max:255',
        'invoiceHeader.last_name' => 'required|string|max:255',
        'invoiceHeader.company_name' => 'nullable|string|max:255',
        'invoiceHeader.email' => 'required|email|unique:mod_invoice_creators,email',
        'invoiceHeader.phone' => 'nullable|string|max:255',
        'invoiceHeader.address' => 'nullable|string|max:255',
        'invoiceHeader.city' => 'nullable|string|max:255',
        'invoiceHeader.postal_code' => 'nullable|string|max:255',
        'invoiceHeader.country' => 'nullable|string|max:255',
        'invoiceHeader.tax_number' => 'nullable|string|max:255',
        'invoiceHeader.bank_name' => 'nullable|string|max:255',
        'invoiceHeader.bank_account' => 'nullable|string|max:255',
        'invoiceHeader.iban' => 'nullable|string|max:255',
        'invoiceHeader.bic' => 'nullable|string|max:255',
        'invoiceHeader.paypal_account' => 'nullable|string|max:255',
        'invoiceHeader.accept_bank_transfer' => 'nullable|boolean',
        'invoiceHeader.accept_paypal' => 'nullable|boolean',
        'invoiceHeader.website' => 'nullable|string|max:255',
        'invoiceHeader.logo_path' => 'nullable|string|max:255',
        'invoiceHeader.notes' => 'nullable|string',
        'logo' => 'nullable|image|max:2048', // Maximal 2 MB für das Logo
    ];

public function mount()
{
    $customerId = Auth::guard('customer')->id();
    $this->invoiceHeaders = ModInvoiceCreator::where('customer_id', $customerId)->get();
}

public function saveInvoiceHeader()
{
    $this->validate();

    // Logo speichern
    if ($this->logo) {
        $logoPath = $this->logo->store('logos', 'public');
        $this->invoiceHeader['logo_path'] = $logoPath;
    }

    // customer_id des angemeldeten Kunden hinzufügen
    $this->invoiceHeader['customer_id'] = Auth::guard('customer')->id();

    // Boolean Defaults
    $this->invoiceHeader['accept_bank_transfer'] = $this->invoiceHeader['accept_bank_transfer'] ?? false;
    $this->invoiceHeader['accept_paypal'] = $this->invoiceHeader['accept_paypal'] ?? false;

    ModInvoiceCreator::updateOrCreate(
        ['id' => $this->invoiceHeader['id'] ?? null],
        $this->invoiceHeader
    );

    $this->reset(['invoiceHeader', 'showForm', 'logo']);

    $customerId = Auth::guard('customer')->id();
    $this->invoiceHeaders = ModInvoiceCreator::where('customer_id', $customerId)->get();

    session()->flash('message', 'Rechnungskopf gespeichert!');
}


    public function edit($id)
    {
        $this->invoiceHeader = ModInvoiceCreator::findOrFail($id)->toArray();
        $this->showForm = true;
    }

public function delete($id)
{
    ModInvoiceCreator::findOrFail($id)->delete();

    $customerId = Auth::guard('customer')->id();
    $this->invoiceHeaders = ModInvoiceCreator::where('customer_id', $customerId)->get();

    session()->flash('message', 'Rechnungskopf gelöscht!');
}


    public function render()
    {
        return view('livewire.backend.e-invoice.invoice-header-manager')
                    ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
