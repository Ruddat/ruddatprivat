<?php

namespace App\Livewire\Backend\Customer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Profile extends Component
{
    public $name;
    public $email;

    public $password;
    public $password_confirmation;

    public $confirmingDelete = false;
    public $deleteCountdown = 0;
    protected $deleteTimer;

    public function mount()
    {
        $user = Auth::guard('customer')->user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile()
    {
        $this->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . Auth::id(),
        ]);

        $user = Auth::guard('customer')->user();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->save();

        session()->flash('success', 'Profil erfolgreich aktualisiert.');
    }

    public function updatePassword()
    {
        $this->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::guard('customer')->user();
        $user->password = Hash::make($this->password);
        $user->save();

        $this->reset(['password', 'password_confirmation']);
        session()->flash('success', 'Passwort erfolgreich geändert.');
    }

public function confirmDelete()
{
    $user = Auth::guard('customer')->user();
    $user->scheduled_for_deletion = true;
    $user->deletion_date = now()->addDays(3);
    $user->save();

    session()->flash('success', 'Dein Account ist zur Löschung vorgemerkt. Die endgültige Löschung erfolgt in 3 Tagen.');
}

public function cancelDelete()
{
    $user = Auth::guard('customer')->user();
    $user->scheduled_for_deletion = false;
    $user->deletion_date = null;
    $user->save();

    session()->flash('success', 'Die Löschung deines Accounts wurde abgebrochen.');
}

    public function deleteAccount()
    {
        $user = Auth::guard('customer')->user();
        Auth::guard('customer')->logout();

        $user->delete();

        return redirect()->route('home')->with('status', 'Dein Account wurde gelöscht.');
    }

    public function render()
    {
        return view('livewire.backend.customer.profile')
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
