<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Customer;

class CustomerContext
{
    /**
     * Liefert die aktuelle Customer-ID, egal ob echter Login oder Impersonate.
     */
    public function id(): ?int
    {
        // ✅ Session hat Vorrang (Impersonate)
        if (session()->has('impersonated_customer_id')) {
            return session('impersonated_customer_id');
        }

        // ✅ Fallback: echter Customer-Login
        if (Auth::guard('customer')->check()) {
            return Auth::guard('customer')->id();
        }

        return null;
    }

    /**
     * Liefert den Customer als Model.
     */
    public function user(): ?Customer
    {
        $id = $this->id();

        return $id ? Customer::find($id) : null;
    }

    /**
     * Prüft, ob gerade impersoniert wird.
     */
    public function isImpersonated(): bool
    {
        return session()->has('impersonated_customer_id')
            && session()->has('impersonate_admin_id');
    }

    /**
     * Liefert die Admin-ID, die den Impersonate gestartet hat.
     */
    public function originalAdminId(): ?int
    {
        return session('impersonate_admin_id');
    }

    /**
     * Beendet das Impersonate und loggt den Admin wieder ein.
     */
    public function stopImpersonate(): void
    {
        if ($this->isImpersonated()) {
            $adminId = $this->originalAdminId();

            // Session bereinigen
            session()->forget(['impersonated_customer_id', 'impersonate_admin_id']);

            // Admin wieder einloggen, falls bekannt
            if ($adminId) {
                Auth::guard('admin')->loginUsingId($adminId);
            }

            // Customer-Guard abmelden
            Auth::guard('customer')->logout();
        }
    }
}
