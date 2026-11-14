<?php

namespace App\Providers;

use App\Models\Customer;
use App\Observers\CustomerObserver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->singleton(\App\Services\CustomerContext::class, function ($app) {
            return new \App\Services\CustomerContext;
        });

        // SMS-Anbieter austauschbar: INQUIRY_SMS_DRIVER=seven (echter Versand) oder log (Entwicklung)
        $this->app->bind(\App\Services\InquiryAlerts\Contracts\SmsGateway::class, function () {
            return match (config('inquiry-alerts.channels.sms.driver')) {
                'seven' => new \App\Services\InquiryAlerts\Sms\SevenSmsGateway,
                default => new \App\Services\InquiryAlerts\Sms\LogSmsGateway,
            };
        });

    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
{
    Customer::observe(\App\Observers\CustomerObserver::class);

    // Prüfen ob settings-Tabelle existiert, bevor Defaults gebootet werden
    if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
        try {
            \App\Helpers\SettingsHelper::bootDefaults();
        } catch (\Throwable $e) {
            // Optional: Loggen, aber Fehler nicht blockieren
            Log::warning('SettingsHelper bootDefaults konnte nicht ausgeführt werden: ' . $e->getMessage());
        }
    }
}


}
