<?php

namespace App\Livewire\Backend\UtilityCosts;

use App\Models\UtilityCosts\RentalObject;
use App\Models\UtilityCosts\TenantPayment;
use App\Models\UtilityCosts\UtilityTenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TenantPayments extends Component
{
    public $tenant_id;
    public $rental_object_id;
    public $year;
    public $month;
    public $amount;
    public $editMode = false;
    public $editId;
    public $sortedByTenant = false;

    public $tenants;
    public $rentalObjects;
    public $payments;
    public $availableYears = [];
    public $availableMonths = [];
    public $payment_date;

    protected $rules = [
        'tenant_id' => 'required|exists:utility_tenants,id',
        'rental_object_id' => 'required|exists:rental_objects,id',
        'year' => 'required|integer|digits:4',
        'month' => 'required|integer|between:1,12',
        'amount' => 'required|numeric|min:0',
        'payment_date' => 'nullable|date',
    ];

    public function mount()
    {
        $this->tenants = UtilityTenant::where('user_id', Auth::guard('customer')->id())->get();
        $this->rentalObjects = RentalObject::where('user_id', Auth::guard('customer')->id())->get();
        $this->loadPayments();

        $this->payment_date = now()->startOfMonth()->toDateString();
    }

    public function updatedTenantId()
    {
        $tenant = UtilityTenant::where('user_id', Auth::guard('customer')->id())->find($this->tenant_id);
        if ($tenant) {
            $this->generateAvailableYearsAndMonths($tenant);
        }
        $this->loadPayments();
    }

    public function updatedYear()
    {
        if ($this->tenant_id) {
            $tenant = UtilityTenant::where('user_id', Auth::guard('customer')->id())->find($this->tenant_id);
            if ($tenant) {
                $this->generateAvailableYearsAndMonths($tenant);
            }
        }
    }

    public function generateAvailableYearsAndMonths($tenant)
    {
        $startDate = Carbon::parse($tenant->start_date);
        $endDate = $tenant->end_date ? Carbon::parse($tenant->end_date) : Carbon::now();

        $this->availableYears = range($startDate->year, $endDate->year);

        if ($this->year == $startDate->year) {
            $this->availableMonths = range($startDate->month, 12);
        } elseif ($this->year == $endDate->year) {
            $this->availableMonths = range(1, $endDate->month);
        } else {
            $this->availableMonths = range(1, 12);
        }

        if (!in_array($this->month, $this->availableMonths)) {
            $this->month = $this->availableMonths[0];
        }
    }

    public function loadPayments()
    {
        $query = TenantPayment::with('tenant', 'rentalObject')
            ->where('user_id', Auth::guard('customer')->id());

        if ($this->tenant_id) {
            $query->where('tenant_id', $this->tenant_id);
        }

        $this->payments = $this->sortedByTenant
            ? $query->orderBy('tenant_id')->get()
            : $query->get();
    }

    public function savePayment()
    {
        $this->validate();

        $customerId = Auth::guard('customer')->id();

        $existingPayment = TenantPayment::where('tenant_id', $this->tenant_id)
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->where('user_id', $customerId)
            ->first();

        if ($existingPayment && !$this->editMode) {
            session()->flash('error', 'Für diesen Monat und Jahr existiert bereits ein Eintrag.');
            return;
        }

        if (!$this->payment_date) {
            $this->payment_date = now()->startOfMonth()->toDateString();
        }

        if ($this->editMode) {
            $payment = TenantPayment::where('user_id', $customerId)->findOrFail($this->editId);
            $payment->update([
                'tenant_id' => $this->tenant_id,
                'rental_object_id' => $this->rental_object_id,
                'year' => $this->year,
                'month' => $this->month,
                'amount' => $this->amount,
                'payment_date' => $this->payment_date,
            ]);
        } else {
            TenantPayment::create([
                'user_id' => $customerId,
                'tenant_id' => $this->tenant_id,
                'rental_object_id' => $this->rental_object_id,
                'year' => $this->year,
                'month' => $this->month,
                'amount' => $this->amount,
                'payment_date' => $this->payment_date,
            ]);
        }

        if ($this->month < 12 && in_array($this->month + 1, $this->availableMonths)) {
            $this->month++;
            $this->payment_date = Carbon::create($this->year, $this->month, 1)->toDateString();
        } else {
            if (in_array($this->year + 1, $this->availableYears)) {
                $this->year++;
                $this->month = 1;
                $this->payment_date = Carbon::create($this->year, $this->month, 1)->toDateString();
            }
        }

        $this->loadPayments();
    }

    public function editPayment($id)
    {
        $payment = TenantPayment::where('user_id', Auth::guard('customer')->id())->findOrFail($id);

        $this->editMode = true;
        $this->editId = $payment->id;
        $this->tenant_id = $payment->tenant_id;
        $this->rental_object_id = $payment->rental_object_id;
        $this->year = $payment->year;
        $this->month = $payment->month;
        $this->amount = $payment->amount;
        $this->payment_date = $payment->payment_date ?? now()->startOfMonth()->toDateString();

        $this->generateAvailableYearsAndMonths($payment->tenant);
    }

    public function resetFields()
    {
        $this->reset(['tenant_id', 'rental_object_id', 'year', 'month', 'amount', 'payment_date', 'editMode', 'editId']);

        if ($this->tenant_id) {
            $tenant = UtilityTenant::where('user_id', Auth::guard('customer')->id())->find($this->tenant_id);
            if ($tenant) {
                $this->generateAvailableYearsAndMonths($tenant);
            }
        }

        $this->payment_date = now()->startOfMonth()->toDateString();
    }

    public function deletePayment($id)
    {
        TenantPayment::where('user_id', Auth::guard('customer')->id())->findOrFail($id)->delete();
        $this->loadPayments();
    }

    public function sortByTenant()
    {
        $this->sortedByTenant = !$this->sortedByTenant;
        $this->loadPayments();
    }

    public function render()
    {
        return view('livewire.backend.utility-costs.tenant-payments')
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
