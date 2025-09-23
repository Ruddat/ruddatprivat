<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;

class ImpersonateController extends Controller
{
    public function start($customerId)
    {
        $customer = Customer::findOrFail($customerId);

        // Admin merken
        session(['impersonator_id' => Auth::guard('admin')->id()]);

        // Customer einloggen
        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard')
            ->with('message', 'Du bist jetzt als Customer eingeloggt.');
    }

    public function stop()
    {
        $adminId = session('impersonator_id');

        if ($adminId) {
            // Admin zurücksetzen
            Auth::guard('admin')->loginUsingId($adminId);
            session()->forget('impersonator_id');

            return redirect()->route('admin.dashboard')
                ->with('message', 'Impersonation beendet.');
        }

        return redirect()->route('admin.login');
    }
}
