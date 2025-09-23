<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Account;
use App\Models\Tenant;
use App\Services\ChartOfAccountsService;
use Livewire\Component;

class AccountManager extends Component
{
    public $tenantId;

    public $number;

    public $name;

    public $type = 'asset';

    public $accountId = null;

    public $framework = 'basic';

    protected function rules()
    {
        return [
            'number' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
        ];
    }

    public function mount()
    {
        $this->tenantId = Tenant::current()?->id;
    }

    public function save()
    {
        $data = $this->validate();
        $data['tenant_id'] = $this->tenantId;

        if ($this->accountId) {
            $account = Account::findOrFail($this->accountId);
            $account->update($data);
            session()->flash('success', 'Konto aktualisiert');
        } else {
            Account::create($data);
            session()->flash('success', 'Konto angelegt');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $acc = Account::findOrFail($id);
        $this->accountId = $acc->id;
        $this->number = $acc->number;
        $this->name = $acc->name;
        $this->type = $acc->type;
    }

    public function delete($id)
    {
        Account::findOrFail($id)->delete();
        session()->flash('success', 'Konto gelöscht');
    }

    public function importFramework()
    {
        ChartOfAccountsService::createForTenant($this->tenantId, $this->framework);
        session()->flash('success', 'Kontorahmen importiert');
    }

    public function resetForm()
    {
        $this->reset(['accountId', 'number', 'name', 'type']);
        $this->type = 'asset';
    }

    public function render()
    {
        $accounts = Account::where('tenant_id', $this->tenantId)->orderBy('number')->get();
        $frameworks = ChartOfAccountsService::getFrameworks();

        return view('livewire.backend.bookkeeping.account-manager', compact('accounts', 'frameworks'))
            ->extends('backend.layouts.backend');
    }
}
