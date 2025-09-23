<?php

namespace App\Livewire\Backend\UtilityCosts;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\UtilityCosts\RentalObject;
use App\Models\UtilityCosts\UtilityTenant;
use App\Models\UtilityCosts\RefundsOrPayments;

class RefundsOrPaymentsComponent extends Component
{
    public $tenant_id, $rental_object_id, $amount, $type, $note, $payment_date;
    public $editMode = false, $editId;
    public $tenants, $rentalObjects, $entries;

    protected $rules = [
        'tenant_id' => 'required|exists:utility_tenants,id', // ✅ korrigiert
        'rental_object_id' => 'required|exists:rental_objects,id',
        'type' => 'required|in:refund,payment',
        'amount' => 'required|numeric|min:0',
        'payment_date' => 'nullable|date',
        'note' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $customerId = Auth::guard('customer')->id(); // ✅ Customer-Guard
        $this->tenants = UtilityTenant::where('user_id', $customerId)->get();
        $this->rentalObjects = RentalObject::where('user_id', $customerId)->get();
        $this->loadEntries();
    }

    public function loadEntries()
    {
        $customerId = Auth::guard('customer')->id(); // ✅
        $this->entries = RefundsOrPayments::with('tenant', 'rentalObject')
            ->where('user_id', $customerId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function saveEntry()
    {
        $this->validate();

        $customerId = Auth::guard('customer')->id(); // ✅
        $paymentDate = $this->payment_date ?? now()->toDateString();
        $year = Carbon::parse($paymentDate)->year;
        $month = Carbon::parse($paymentDate)->month;

        if ($this->editMode) {
            RefundsOrPayments::where('user_id', $customerId) // ✅ Absicherung
                ->findOrFail($this->editId)
                ->update([
                    'tenant_id' => $this->tenant_id,
                    'rental_object_id' => $this->rental_object_id,
                    'type' => $this->type,
                    'amount' => $this->amount,
                    'payment_date' => $paymentDate,
                    'year' => $year,
                    'month' => $month,
                    'note' => $this->note,
                ]);
        } else {
            RefundsOrPayments::create([
                'user_id' => $customerId,
                'tenant_id' => $this->tenant_id,
                'rental_object_id' => $this->rental_object_id,
                'type' => $this->type,
                'amount' => $this->amount,
                'payment_date' => $paymentDate,
                'year' => $year,
                'month' => $month,
                'note' => $this->note,
            ]);
        }

        $this->resetFields();
        $this->loadEntries();
    }

    public function editEntry($id)
    {
        $customerId = Auth::guard('customer')->id(); // ✅
        $entry = RefundsOrPayments::where('user_id', $customerId)->findOrFail($id);

        $this->editMode = true;
        $this->editId = $entry->id;
        $this->tenant_id = $entry->tenant_id;
        $this->rental_object_id = $entry->rental_object_id;
        $this->type = $entry->type;
        $this->amount = $entry->amount;
        $this->payment_date = $entry->payment_date;
        $this->note = $entry->note;
    }

    public function resetFields()
    {
        $this->reset(['tenant_id', 'rental_object_id', 'type', 'amount', 'payment_date', 'note', 'editMode', 'editId']);
    }

    public function deleteEntry($id)
    {
        $customerId = Auth::guard('customer')->id(); // ✅
        RefundsOrPayments::where('user_id', $customerId)->findOrFail($id)->delete();
        $this->loadEntries();
    }

    public function render()
    {
        return view('livewire.backend.utility-costs.refunds-or-payments-component', [
            'entries' => $this->entries,
        ])
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
