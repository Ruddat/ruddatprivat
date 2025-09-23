<?php

namespace App\Livewire\Backend\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdminLogin extends Component
{
    public $email = '';

    public $password = '';

    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::guard('admin')->attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        $this->addError('email', 'Login fehlgeschlagen.');
    }

    public function render()
    {
        return view('livewire.backend.auth.admin-login')
            ->layout('layouts.app', ['title' => 'Admin Login']);
    }
}
