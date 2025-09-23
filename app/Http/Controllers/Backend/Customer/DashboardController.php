<?php

namespace App\Http\Controllers\Backend\Customer;

use Illuminate\Http\Request;
use App\Helpers\SettingsHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
public function index()
{
    $customer = Auth::guard('customer')->user();

    $limits = [
        'invoices' => [
            'label' => 'Rechnungen',
            'used'  => 12, // TODO: aus DB zählen
            'max'   => (int) SettingsHelper::get('limits.invoices', 0),
        ],
        'tenants' => [
            'label' => 'Mieter',
            'used'  => 5, // TODO: aus DB zählen
            'max'   => (int) SettingsHelper::get('limits.tenants', 0),
        ],
        'storage' => [
            'label' => 'Speicher (MB)',
            'used'  => 1024, // TODO: genutzter Speicher in MB berechnen
            'max'   => (int) SettingsHelper::get('limits.storage', 0),
        ],
    ];

    return view('backend.customer.dashboard', [
        'customer' => $customer,
        'limits'   => $limits,
    ]);
}

public function buchhaltung()
{
    $customer = Auth::guard('customer')->user();
    return view('backend.customer.dashboards.buchhaltung', compact('customer'));
}

public function rechnungen()
{
    $customer = Auth::guard('customer')->user();
    return view('backend.customer.dashboards.rechnungen', compact('customer'));
}

public function nebenkosten()
{
    $customer = Auth::guard('customer')->user();
    return view('backend.customer.dashboards.nebenkosten', compact('customer'));
}



}