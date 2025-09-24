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

    // Letzte 5 Rechnungen
    $latestInvoices = \App\Models\ModInvoice::with('recipient')
        ->where('customer_id', $customer->id)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    // Summen nach Status
    $openAmount = \App\Models\ModInvoice::where('customer_id', $customer->id)
        ->where('status', '!=', 'paid')
        ->sum('total_amount');

    $paidAmount = \App\Models\ModInvoice::where('customer_id', $customer->id)
        ->where('status', 'paid')
        ->sum('total_amount');

    $invoiceStats = [
        'draft'     => \App\Models\ModInvoice::where('customer_id', $customer->id)->where('status', 'draft')->count(),
        'sent'      => \App\Models\ModInvoice::where('customer_id', $customer->id)->where('status', 'sent')->count(),
        'paid'      => \App\Models\ModInvoice::where('customer_id', $customer->id)->where('status', 'paid')->count(),
        'cancelled' => \App\Models\ModInvoice::where('customer_id', $customer->id)->where('status', 'cancelled')->count(),
    ];

    // Checks für Onboarding
    $invoiceCreatorsCount = \App\Models\ModInvoiceCreator::where('customer_id', $customer->id)->count();
    $recipientsCount      = \App\Models\ModInvoiceRecipient::where('customer_id', $customer->id)->count();

    return view('backend.customer.dashboards.rechnungen', compact(
        'customer',
        'latestInvoices',
        'openAmount',
        'paidAmount',
        'invoiceStats',
        'invoiceCreatorsCount',
        'recipientsCount'
    ));
}


public function nebenkosten()
{
    $customer = Auth::guard('customer')->user();
    return view('backend.customer.dashboards.nebenkosten', compact('customer'));
}



}