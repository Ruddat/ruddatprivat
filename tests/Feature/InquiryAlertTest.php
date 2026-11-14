<?php

namespace Tests\Feature;

use App\Jobs\SendInquiryAlert;
use App\Livewire\Backend\Admin\Inquiries\AlertSettingsPage;
use App\Mail\InquiryAlertMail;
use App\Models\Admin;
use App\Models\Inquiry;
use App\Models\InquiryAlert;
use App\Services\InquiryAlerts\AlertDispatcher;
use App\Services\InquiryAlerts\AlertMessage;
use App\Services\InquiryAlerts\AlertSettings;
use App\Services\Inquiries\InquirySubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

class InquiryAlertTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'inquiry-alerts.channels.mail.enabled' => true,
            'inquiry-alerts.channels.mail.to' => 'service@example.com',
            'inquiry-alerts.channels.sms.enabled' => false,
            'inquiry-alerts.channels.sms.to' => '+4917612345678',
            'inquiry-alerts.channels.sms.driver' => 'seven',
            'inquiry-alerts.channels.telegram.enabled' => false,
            'inquiry-alerts.channels.telegram.chat_id' => '12345',
            'inquiry-alerts.channels.whatsapp.enabled' => false,
            'services.seven.api_key' => 'test-key',
            'services.seven.endpoint' => 'https://gateway.seven.io/api/sms',
            'services.telegram.bot_token' => '1:test',
        ]);

        Http::preventStrayRequests();
    }

    private function submit(array $overrides = []): Inquiry
    {
        return app(InquirySubmission::class)->submit(array_merge([
            'name' => 'Erika Muster',
            'company' => 'Musterfirma GmbH',
            'email' => 'erika@example.com',
            'phone' => '+49 30 123',
            'type' => 'managed_services',
            'application_summary' => 'Vertrauliche Beschreibung der Anwendung mit Interna',
            'requirements' => null,
        ], $overrides))['inquiry'];
    }

    public function test_new_inquiry_queues_one_alert_per_enabled_channel(): void
    {
        Queue::fake();
        config(['inquiry-alerts.channels.sms.enabled' => true]);

        $inquiry = $this->submit();

        $this->assertEqualsCanonicalizing(['mail', 'sms'], $inquiry->alerts()->pluck('channel')->all());
        $this->assertSame(['queued'], $inquiry->alerts()->distinct()->pluck('status')->all());
        Queue::assertPushed(SendInquiryAlert::class, 2);
    }

    public function test_inquiry_is_stored_even_if_alert_dispatch_queue_is_down(): void
    {
        // Speicherung zuerst: auch ohne Kanäle bleibt die Anfrage erhalten
        config(['inquiry-alerts.channels.mail.enabled' => false]);
        $inquiry = $this->submit();

        $this->assertNotNull($inquiry->id);
        $this->assertSame(0, $inquiry->alerts()->count());
    }

    public function test_mail_alert_is_sent_and_marked_accepted(): void
    {
        Mail::fake();

        $inquiry = $this->submit(); // QUEUE_CONNECTION=sync im Test

        Mail::assertSent(InquiryAlertMail::class, function ($mail) use ($inquiry) {
            return $mail->hasTo('service@example.com') && $mail->alert->reference === $inquiry->reference;
        });

        $alert = $inquiry->alerts()->sole();
        $this->assertSame(InquiryAlert::ACCEPTED, $alert->status);
        $this->assertNotNull($alert->accepted_at);
        $this->assertSame('se…@example.com', $alert->recipient_masked);
    }

    public function test_sms_via_seven_contains_no_personal_or_application_data(): void
    {
        Mail::fake();
        config(['inquiry-alerts.channels.sms.enabled' => true]);
        Http::fake(['gateway.seven.io/*' => Http::response(['success' => '100', 'messages' => [['id' => '77']]])]);

        $inquiry = $this->submit(['company' => 'Musterfirma GmbH https://evil.example <script>']);

        Http::assertSent(function (Request $request) {
            $text = $request['text'];

            return $request->hasHeader('X-Api-Key', 'test-key')
                && $request['to'] === '+4917612345678'
                && $text === 'RuddatTech: Neue Anfrage von Musterfirma GmbH script. Thema: Managed Application Services. Details im Adminbereich.'
                && ! str_contains($text, 'Erika')
                && ! str_contains($text, 'erika@example.com')
                && ! str_contains($text, 'Vertrauliche');
        });

        $sms = $inquiry->alerts()->where('channel', 'sms')->sole();
        $this->assertSame(InquiryAlert::ACCEPTED, $sms->status);
        $this->assertSame('77', $sms->provider_message_id);
    }

    public function test_seven_error_code_is_treated_as_failure_and_retried(): void
    {
        Queue::fake();
        config(['inquiry-alerts.channels.sms.enabled' => true, 'inquiry-alerts.channels.mail.enabled' => false]);
        Http::fake(['gateway.seven.io/*' => Http::response(['success' => '900'])]);

        $alert = $this->submit()->alerts()->sole();

        $job = new SendInquiryAlert($alert->id);
        try {
            app()->call([$job, 'handle']);
            $this->fail('Exception erwartet');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Code 900', $e->getMessage());
        }

        $alert->refresh();
        $this->assertSame(InquiryAlert::QUEUED, $alert->status); // bleibt für Wiederholung offen
        $this->assertSame(1, $alert->attempts);
        $this->assertStringContainsString('Code 900', $alert->last_error);
        $this->assertSame([30, 120, 600, 1800], $job->backoff());
        $this->assertSame(5, $job->tries);
    }

    public function test_final_failure_marks_failed_and_triggers_mail_fallback_once(): void
    {
        Queue::fake();
        config(['inquiry-alerts.channels.sms.enabled' => true, 'inquiry-alerts.channels.mail.enabled' => true]);

        $inquiry = $this->submit();
        $sms = $inquiry->alerts()->where('channel', 'sms')->sole();

        (new SendInquiryAlert($sms->id))->failed(new RuntimeException('Gateway down'));
        (new SendInquiryAlert($sms->id))->failed(new RuntimeException('Gateway down')); // zweimal → nur ein Fallback

        $this->assertSame(InquiryAlert::FAILED, $sms->fresh()->status);
        $this->assertNotNull($sms->fresh()->failed_at);

        $fallbacks = $inquiry->alerts()->where('is_fallback', true)->get();
        $this->assertCount(1, $fallbacks);
        $this->assertSame('mail', $fallbacks->first()->channel);
    }

    public function test_fallback_is_not_chained_endlessly(): void
    {
        Queue::fake();
        config(['inquiry-alerts.channels.sms.enabled' => true]);
        $inquiry = $this->submit();
        $mail = $inquiry->alerts()->where('channel', 'mail')->sole();

        (new SendInquiryAlert($mail->id))->failed(new RuntimeException('SMTP down'));
        $fallback = $inquiry->alerts()->where('is_fallback', true)->sole();
        $this->assertSame('sms', $fallback->channel);

        (new SendInquiryAlert($fallback->id))->failed(new RuntimeException('down'));
        $this->assertSame(1, $inquiry->alerts()->where('is_fallback', true)->count());
    }

    public function test_alerts_are_not_duplicated_for_the_same_inquiry(): void
    {
        Queue::fake();
        $inquiry = $this->submit();

        app(AlertDispatcher::class)->dispatchForInquiry($inquiry);
        app(AlertDispatcher::class)->dispatchForInquiry($inquiry);

        $this->assertSame(1, $inquiry->alerts()->count());
        Queue::assertPushed(SendInquiryAlert::class, 1);
    }

    public function test_accepted_alert_is_not_sent_again(): void
    {
        Mail::fake();
        $alert = $this->submit()->alerts()->sole();
        Mail::assertSentCount(1);

        app()->call([new SendInquiryAlert($alert->id), 'handle']);
        Mail::assertSentCount(1);
    }

    public function test_enabled_but_unconfigured_channel_is_skipped_without_request(): void
    {
        Queue::fake();
        config(['inquiry-alerts.channels.sms.enabled' => true, 'inquiry-alerts.channels.sms.to' => 'kaputt']);

        $sms = $this->submit()->alerts()->where('channel', 'sms')->sole();

        $this->assertSame(InquiryAlert::SKIPPED, $sms->status);
        Http::assertNothingSent();
    }

    public function test_log_driver_never_calls_seven(): void
    {
        Mail::fake();
        config(['inquiry-alerts.channels.sms.enabled' => true, 'inquiry-alerts.channels.sms.driver' => 'log']);

        $sms = $this->submit()->alerts()->where('channel', 'sms')->sole();

        Http::assertNothingSent();
        $this->assertSame('log', $sms->provider);
        $this->assertSame(InquiryAlert::ACCEPTED, $sms->status);
    }

    public function test_telegram_sends_short_text_only(): void
    {
        config(['inquiry-alerts.channels.mail.enabled' => false, 'inquiry-alerts.channels.telegram.enabled' => true]);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true, 'result' => ['message_id' => 5]])]);

        $alert = $this->submit()->alerts()->sole();

        Http::assertSent(fn (Request $r) => $r['chat_id'] === '12345'
            && str_starts_with($r['text'], 'RuddatTech: Neue Anfrage von Musterfirma GmbH.')
            && ! str_contains($r['text'], 'Erika'));
        $this->assertSame(InquiryAlert::ACCEPTED, $alert->fresh()->status);
    }

    public function test_telegram_token_is_not_stored_in_error_log(): void
    {
        Queue::fake();
        config(['inquiry-alerts.channels.mail.enabled' => false, 'inquiry-alerts.channels.telegram.enabled' => true, 'services.telegram.bot_token' => '123456:SECRET-token']);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => false], 401)]);

        $alert = $this->submit()->alerts()->sole();
        try {
            app()->call([new SendInquiryAlert($alert->id), 'handle']);
        } catch (RuntimeException) {
        }

        $this->assertStringNotContainsString('SECRET', (string) $alert->fresh()->last_error);
    }

    public function test_whatsapp_stays_inactive_without_configuration(): void
    {
        $this->actingAs(Admin::create(['name' => 'A', 'email' => 'a@example.com', 'password' => bcrypt('x-test')]), 'admin');

        Livewire::test(AlertSettingsPage::class)
            ->set('enabled.whatsapp', true)
            ->call('save')
            ->assertHasErrors('enabled.whatsapp')
            ->assertSet('enabled.whatsapp', false);

        $this->assertFalse(app(AlertSettings::class)->enabled('whatsapp'));
    }

    public function test_admin_can_change_recipients_and_send_test(): void
    {
        Queue::fake();
        $this->actingAs(Admin::create(['name' => 'A', 'email' => 'a@example.com', 'password' => bcrypt('x-test')]), 'admin');

        Livewire::test(AlertSettingsPage::class)
            ->set('recipients.sms', '0176 kaputt')
            ->call('save')
            ->assertHasErrors('recipients.sms')
            ->set('recipients.sms', '+4915112345678')
            ->set('enabled.sms', true)
            ->call('save')
            ->assertHasNoErrors()
            ->call('sendTest', 'sms');

        $settings = app(AlertSettings::class);
        $this->assertSame('+4915112345678', $settings->recipient('sms'));
        $this->assertTrue($settings->enabled('sms'));

        $test = InquiryAlert::where('is_test', true)->sole();
        $this->assertSame('sms', $test->channel);
        $this->assertNull($test->inquiry_id);
        Queue::assertPushed(SendInquiryAlert::class);
    }

    public function test_alert_settings_page_requires_admin(): void
    {
        $this->get(route('admin.inquiry-alerts.settings'))->assertRedirect();
    }

    public function test_status_labels_never_claim_reading(): void
    {
        $alert = new InquiryAlert(['status' => InquiryAlert::ACCEPTED]);
        $this->assertSame('Vom Anbieter angenommen', $alert->statusLabel());
        $this->assertStringNotContainsStringIgnoringCase('gelesen', $alert->statusLabel());
    }

    public function test_whatsapp_button_only_with_configured_number(): void
    {
        $this->get('/')->assertDontSee('wa.me', false);

        config(['inquiry-alerts.whatsapp_button_number' => '+49 151 1234 5678']);
        $this->get('/')->assertSee('https://wa.me/4915112345678', false);
    }

    public function test_sanitize_strips_links_and_limits_length(): void
    {
        $this->assertSame('ACME', AlertMessage::sanitize("ACME\nhttps://x.y/z"));
        $this->assertSame('unbekannt', AlertMessage::sanitize('<<>>'));
        $this->assertSame(41, mb_strlen(AlertMessage::sanitize(str_repeat('a', 100))));
    }
}
