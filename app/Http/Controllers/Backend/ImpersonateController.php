<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function start(
        Request $request,
        Customer $customer
    ): RedirectResponse {
        abort_unless(Auth::guard('admin')->check(), 403);

        if ($request->session()->has('impersonator_id')) {
            return redirect()
                ->route('customer.dashboard')
                ->with('message', 'Eine Kundenansicht ist bereits aktiv.');
        }

        $request->session()->put(
            'impersonator_id',
            Auth::guard('admin')->id()
        );

        Auth::guard('customer')->login($customer);

        $request->session()->regenerate();

        return redirect()
            ->route('customer.dashboard')
            ->with(
                'message',
                "Du arbeitest jetzt als Customer {$customer->name}."
            );
    }

    public function stop(Request $request): RedirectResponse
    {
        abort_unless(
            Auth::guard('admin')->check()
            && $request->session()->has('impersonator_id'),
            403
        );

        Auth::guard('customer')->logout();

        $request->session()->forget('impersonator_id');
        $request->session()->regenerate();

        return redirect()
            ->route('admin.customers.index')
            ->with('message', 'Kundenansicht wurde beendet.');
    }
}
