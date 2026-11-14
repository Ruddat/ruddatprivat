<?php

namespace Tests\Feature;

use App\Enums\InquiryStatus;
use App\Events\InquiryReceived;
use App\Livewire\Backend\Admin\Inquiries\InquiryIndex;
use App\Livewire\Backend\Admin\Inquiries\InquiryShow;
use App\Livewire\Site\InquiryForm;
use App\Models\Admin;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    private function validForm()
    {
        $this->travel(-10)->seconds();
        $component = Livewire::test(InquiryForm::class);
        $this->travelBack();

        return $component
            ->set('name', 'Erika Muster')
            ->set('company', 'Musterfirma GmbH')
            ->set('email', 'erika@example.com')
            ->set('phone', '+49 531 123456')
            ->set('type', 'managed_services')
            ->set('application_summary', 'Laravel-Kundenportal mit Bestellprozess, PHP 8.1, läuft auf Plesk.')
            ->set('requirements', 'Updates und Backups')
            ->set('privacy', true);
    }

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('inquiry:ip:' . sha1('127.0.0.1'));
    }

    public function test_contact_page_renders_form(): void
    {
        $this->get(route('kontakt'))->assertOk()->assertSeeLivewire(InquiryForm::class);
    }

    public function test_contact_page_preselects_type_from_query(): void
    {
        Livewire::test(InquiryForm::class, ['preset' => 'takeover'])->assertSet('type', 'takeover');
        Livewire::test(InquiryForm::class, ['preset' => 'quatsch'])->assertSet('type', '');
        $this->get(route('kontakt', ['anfrage' => ['x']]))->assertOk();
    }

    public function test_valid_inquiry_is_stored_and_event_dispatched(): void
    {
        Event::fake([InquiryReceived::class]);

        $this->validForm()->call('submit')->assertHasNoErrors()->assertSet('submitted', true);

        $inquiry = Inquiry::sole();
        $this->assertSame('Musterfirma GmbH', $inquiry->company);
        $this->assertSame(InquiryStatus::New, $inquiry->status);
        $this->assertMatchesRegularExpression('/^RT-\d{4}-\d{4}$/', $inquiry->reference);
        $this->assertNotNull($inquiry->privacy_accepted_at);
        $this->assertNotSame('127.0.0.1', $inquiry->ip_hash);

        Event::assertDispatched(InquiryReceived::class, fn ($e) => $e->inquiry->is($inquiry));
    }

    public function test_validation_errors(): void
    {
        $this->travel(-10)->seconds();
        $component = Livewire::test(InquiryForm::class);
        $this->travelBack();

        $component->set('email', 'keine-mail')
            ->set('phone', 'abc')
            ->set('type', 'unbekannt')
            ->set('application_summary', 'zu kurz')
            ->call('submit')
            ->assertHasErrors(['name', 'company', 'email', 'phone', 'type', 'application_summary', 'privacy']);

        $this->assertSame(0, Inquiry::count());
    }

    public function test_honeypot_is_silently_discarded(): void
    {
        Event::fake([InquiryReceived::class]);

        $this->validForm()->set('website', 'https://spam.example')->call('submit')->assertSet('submitted', true);

        $this->assertSame(0, Inquiry::count());
        Event::assertNotDispatched(InquiryReceived::class);
    }

    public function test_too_fast_submission_is_discarded(): void
    {
        Livewire::test(InquiryForm::class)
            ->set('name', 'Bot')->set('company', 'Bot')->set('email', 'bot@example.com')
            ->set('type', 'other')->set('application_summary', str_repeat('x', 30))->set('privacy', true)
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertSame(0, Inquiry::count());
    }

    public function test_identical_inquiry_is_not_stored_twice(): void
    {
        Event::fake([InquiryReceived::class]);

        $this->validForm()->call('submit');
        $this->validForm()->call('submit')->assertSet('submitted', true);

        $this->assertSame(1, Inquiry::count());
        Event::assertDispatchedTimes(InquiryReceived::class, 1);
    }

    public function test_rate_limit_per_email(): void
    {
        for ($i = 1; $i <= InquiryForm::MAX_PER_EMAIL_PER_HOUR; $i++) {
            $this->validForm()->set('application_summary', "Anwendung Nummer $i mit ausreichend langer Beschreibung")->call('submit');
        }

        $this->validForm()->set('application_summary', 'Noch eine weitere, ausreichend lange Beschreibung')
            ->call('submit')
            ->assertHasErrors('form');

        $this->assertSame(InquiryForm::MAX_PER_EMAIL_PER_HOUR, Inquiry::count());
    }

    public function test_admin_pages_require_login(): void
    {
        $inquiry = $this->makeInquiry();

        $this->get(route('admin.inquiries.index'))->assertRedirect();
        $this->get(route('admin.inquiries.show', $inquiry))->assertRedirect();

        // Ein Kunde ist kein Admin
        $this->assertGuest('admin');
    }

    public function test_admin_sees_inbox_and_can_open_inquiry(): void
    {
        $inquiry = $this->makeInquiry();
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('secret-test')]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.inquiries.index'))
            ->assertOk()
            ->assertSee('Musterfirma GmbH');

        $this->actingAs($admin, 'admin')->get(route('admin.inquiries.show', $inquiry))->assertOk();
        $this->assertNotNull($inquiry->fresh()->first_viewed_at);
    }

    public function test_admin_can_change_status_add_note_and_archive(): void
    {
        $inquiry = $this->makeInquiry();
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('secret-test')]);
        $this->actingAs($admin, 'admin');

        Livewire::test(InquiryShow::class, ['inquiry' => $inquiry])
            ->set('status', 'question')
            ->set('internal_notes', 'Rückruf vereinbart')
            ->call('save')
            ->assertHasNoErrors();

        $inquiry->refresh();
        $this->assertSame(InquiryStatus::Question, $inquiry->status);
        $this->assertSame('Rückruf vereinbart', $inquiry->internal_notes);
        $this->assertNotNull($inquiry->status_changed_at);

        Livewire::test(InquiryShow::class, ['inquiry' => $inquiry])->call('archive');
        $this->assertSame(InquiryStatus::Archived, $inquiry->fresh()->status);
        $this->assertNotNull($inquiry->fresh()->archived_at);
    }

    public function test_inbox_filters_and_stats(): void
    {
        $this->makeInquiry(['company' => 'Alpha AG']);
        $this->makeInquiry(['company' => 'Beta GmbH', 'status' => 'archived', 'fingerprint' => 'b']);
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('secret-test')]);
        $this->actingAs($admin, 'admin');

        Livewire::test(InquiryIndex::class)
            ->assertSee('Alpha AG')
            ->assertDontSee('Beta GmbH') // Standardfilter: offen
            ->set('status', 'all')
            ->assertSee('Beta GmbH')
            ->set('search', 'Beta')
            ->assertDontSee('Alpha AG')
            ->assertViewHas('stats', fn ($s) => $s['new'] === 1 && $s['open'] === 1 && $s['last30'] === 2);
    }

    private function makeInquiry(array $overrides = []): Inquiry
    {
        return Inquiry::create(array_merge([
            'name' => 'Erika Muster',
            'company' => 'Musterfirma GmbH',
            'email' => 'erika@example.com',
            'type' => 'maintenance',
            'application_summary' => 'Laravel-Anwendung',
            'privacy_accepted_at' => now(),
            'fingerprint' => 'a',
        ], $overrides));
    }
}
