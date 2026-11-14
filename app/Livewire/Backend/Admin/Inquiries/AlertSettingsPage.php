<?php

namespace App\Livewire\Backend\Admin\Inquiries;

use App\Models\InquiryAlert;
use App\Services\InquiryAlerts\AlertDispatcher;
use App\Services\InquiryAlerts\AlertSettings;
use App\Services\InquiryAlerts\ChannelRegistry;
use App\Services\InquiryAlerts\Channels\SmsChannel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class AlertSettingsPage extends Component
{
    /** @var array<string, bool> */
    public array $enabled = [];

    /** @var array<string, string> */
    public array $recipients = [];

    public function mount(AlertSettings $settings): void
    {
        foreach (AlertSettings::CHANNELS as $key) {
            $this->enabled[$key] = $settings->enabled($key);
            $this->recipients[$key] = (string) $settings->recipient($key);
        }
    }

    public function save(AlertSettings $settings, ChannelRegistry $channels): void
    {
        $this->validate([
            'recipients.mail' => ['nullable', 'email:rfc', 'max:190'],
            'recipients.sms' => ['nullable', 'regex:/^\+[1-9]\d{6,14}$/'],
            'recipients.whatsapp' => ['nullable', 'regex:/^\+[1-9]\d{6,14}$/'],
            'enabled.*' => ['boolean'],
        ], [
            'recipients.mail.email' => 'Bitte eine gültige E-Mail-Adresse angeben.',
            'recipients.sms.regex' => 'Bitte im internationalen Format angeben, z. B. +4917612345678.',
            'recipients.whatsapp.regex' => 'Bitte im internationalen Format angeben, z. B. +4917612345678.',
        ]);

        foreach (AlertSettings::EDITABLE_RECIPIENTS as $key) {
            $settings->setRecipient($key, trim($this->recipients[$key] ?? '') ?: null);
        }

        foreach (AlertSettings::CHANNELS as $key) {
            $wanted = (bool) ($this->enabled[$key] ?? false);

            // Externe Dienste nur mit gültiger Konfiguration aktivieren
            if ($wanted && ! $channels->get($key)->isConfigured()) {
                $this->enabled[$key] = false;
                $this->addError("enabled.$key", 'Kanal ist nicht vollständig konfiguriert und bleibt deaktiviert.');
                $wanted = false;
            }

            $settings->setEnabled($key, $wanted);
        }

        session()->flash('success', 'Einstellungen gespeichert.');
    }

    public function sendTest(string $key, AlertDispatcher $dispatcher, ChannelRegistry $channels): void
    {
        abort_unless(in_array($key, AlertSettings::CHANNELS, true), 404);

        if (! $channels->get($key)->isConfigured()) {
            session()->flash('error', InquiryAlert::channelLabel($key) . ' ist nicht vollständig konfiguriert.');

            return;
        }

        // Schutz vor versehentlichem Mehrfachversand (kostenpflichtige Kanäle)
        if (RateLimiter::tooManyAttempts("alert-test:$key", 3)) {
            session()->flash('error', 'Bitte einige Minuten warten, bevor erneut getestet wird.');

            return;
        }
        RateLimiter::hit("alert-test:$key", 600);

        $dispatcher->dispatchTest($key);
        session()->flash('success', 'Testbenachrichtigung über ' . InquiryAlert::channelLabel($key) . ' wurde angestoßen. Der Status erscheint unten.');
    }

    public function render(ChannelRegistry $channels)
    {
        $pendingJobs = Schema::hasTable('jobs')
            ? DB::table('jobs')->where('created_at', '<', now()->subMinutes(2)->getTimestamp())->count()
            : 0;

        return view('livewire.backend.admin.inquiries.alert-settings', [
            'channels' => $channels->all(),
            'editable' => AlertSettings::EDITABLE_RECIPIENTS,
            'alerts' => InquiryAlert::with('inquiry:id,reference,company')->latest('id')->limit(25)->get(),
            'stalledJobs' => $pendingJobs,
            'lastAccepted' => InquiryAlert::whereNotNull('accepted_at')->max('accepted_at'),
            'queueConnection' => config('queue.default'),
            'smsDriver' => config('inquiry-alerts.channels.sms.driver'),
            'smsValid' => SmsChannel::isValidNumber($this->recipients['sms'] ?? null),
        ])->extends('backend.admin.layouts.app')
            ->section('content');
    }
}
