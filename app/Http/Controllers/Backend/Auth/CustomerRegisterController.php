<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class CustomerRegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.customer-register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($customer)); // für Mail-Verifikation, Notifications etc.

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard');
    }
}