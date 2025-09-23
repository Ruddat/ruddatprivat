<?php

namespace App\Livewire\Backend\Admin\Customer;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomersTable extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $showCreateModal = false;

    public $name;

    public $email;

    public $password;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:customers,email',
        'password' => 'required|min:6',
    ];

    public function render()
    {
        $customers = Customer::query()
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->paginate($this->perPage);

        return view('livewire.backend.admin.customer.customers-table', [
            'customers' => $customers,
        ]);
    }

    public function createCustomer()
    {
        $this->validate();

        Customer::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        $this->reset(['name', 'email', 'password']);
        $this->showCreateModal = false;

        session()->flash('success', 'Customer erfolgreich angelegt.');
    }

    public function impersonate($customerId)
    {
        $customer = \App\Models\Customer::findOrFail($customerId);

        session([
            'impersonate_admin_id' => Auth::guard('admin')->id(),
            'impersonated_customer_id' => $customer->id,
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard');
    }
}
