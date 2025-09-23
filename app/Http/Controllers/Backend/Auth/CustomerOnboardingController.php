<?php

namespace App\Http\Controllers\Backend\Auth; // <- Großes A

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerVerification;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PhoneVerificationNotification;

class CustomerOnboardingController extends Controller
{
    public function show()
    {
        return view('auth.customer-onboarding');
    }

public function store(Request $request)
{
    $data = $request->validate([
        'street' => 'required|string|max:255',
        'house_number' => 'required|string|max:20',
        'zip' => 'required|string|max:20',
        'city' => 'required|string|max:100',
        'phone' => 'required|string|max:30',
    ]);

    $customer = Auth::guard('customer')->user();
    $customer->update($data);

    // Code erzeugen
    $code = rand(100000, 999999);

    CustomerVerification::updateOrCreate(
        ['customer_id' => $customer->id],
        ['phone' => $data['phone'], 'code' => $code, 'verified' => false]
    );

    // SMS direkt senden
    $sms = new PhoneVerificationNotification($code);
    $sms->sendSms($customer->phone);

    return redirect()->route('customer.dashboard')
        ->with('verify_phone', true);
}

public function verifyCode(Request $request)
{
    $data = $request->validate([
        'code' => 'required|digits:6',
    ]);

    $customer = Auth::guard('customer')->user();
    $verification = CustomerVerification::where('customer_id', $customer->id)->first();

    if ($verification && $verification->code === $data['code']) {
        $verification->verified = true;
        $verification->save();

        $customer->onboarding_done = true;
        $customer->save();

        session()->forget('needs_onboarding');

        return redirect()->route('customer.dashboard')->with('success', 'Telefonnummer bestätigt ✅');
    }

    return back()->withErrors(['code' => 'Falscher Code, bitte erneut versuchen.']);
}
}