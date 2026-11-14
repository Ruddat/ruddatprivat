@extends('site.layouts.app')

@section('title', 'Kontakt – technische Betreuung anfragen | RuddatTech')
@section('meta_description', 'Fragen Sie Wartung, Managed Application Services oder die Übernahme Ihrer bestehenden Webanwendung an. Persönliche Rückmeldung von RuddatTech.')

@section('content')
    @include('site.partials.page-hero', [
        'eyebrow' => 'Kontakt',
        'title' => 'Technische Betreuung anfragen',
        'lead' => 'Beschreiben Sie kurz Ihre Anwendung und Ihr Anliegen. Sie erhalten eine persönliche Rückmeldung – in der Regel mit einem Vorschlag für ein Erstgespräch.',
        'breadcrumbs' => ['Startseite' => route('home'), 'Kontakt' => null],
    ])

    <section class="bg-white py-20 sm:py-24">
        <div class="site-container grid gap-12 lg:grid-cols-[0.8fr_1.2fr]">
            <aside class="space-y-8">
                <div>
                    <h2 class="text-lg font-semibold text-navy-900">Direkter Kontakt</h2>
                    <a href="mailto:{{ \App\Support\Site\SiteContent::CONTACT_EMAIL }}"
                        class="mt-4 inline-flex items-center gap-3 text-anthracite-800 hover:text-brass-700">
                        <span class="icon-tile h-10 w-10">@include('site.partials.icon', ['name' => 'mail', 'class' => 'h-4 w-4'])</span>
                        {{ \App\Support\Site\SiteContent::CONTACT_EMAIL }}
                    </a>
                </div>

                @if (strlen(preg_replace('/\D/', '', (string) config('inquiry-alerts.whatsapp_button_number'))) >= 8)
                    <div class="rounded-lg border border-slate-200 p-6">
                        <h2 class="text-base font-semibold text-navy-900">Per WhatsApp</h2>
                        <p class="mt-2 text-[0.95rem] leading-7 text-anthracite-700">
                            Über den grünen Button können Sie direkt einen WhatsApp-Chat öffnen. Bitte beachten Sie:
                            Solche Nachrichten laufen über WhatsApp (Meta) und werden nicht im Anfragesystem erfasst.
                            Für eine dokumentierte Anfrage nutzen Sie bitte das Formular.
                        </p>
                    </div>
                @endif

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-base font-semibold text-navy-900">Hilfreiche Angaben</h2>
                    <ul class="check-list mt-4 space-y-3 text-[0.95rem] leading-7 text-anthracite-700">
                        <li>Wofür wird die Anwendung genutzt, und von wem?</li>
                        <li>Auf welcher Technik basiert sie (z. B. PHP-/Laravel-Version), soweit bekannt?</li>
                        <li>Wo wird sie betrieben, und wer betreut sie bisher?</li>
                        <li>Gibt es aktuell ein konkretes Problem oder eine Frist?</li>
                    </ul>
                    <p class="mt-5 text-sm leading-6 text-slate-500">
                        Bitte senden Sie keine Zugangsdaten, Passwörter oder vertraulichen Kundendaten über das Formular.
                    </p>
                </div>

                <div class="rounded-lg border border-brass-200 bg-brass-50 p-6">
                    <h2 class="text-base font-semibold text-navy-900">Akute Störung?</h2>
                    <p class="mt-2 text-[0.95rem] leading-7 text-anthracite-700">
                        Wählen Sie im Formular „Technische Störung“. Kurzfristige Hilfe ist nach Verfügbarkeit möglich;
                        garantierte Reaktionszeiten gelten nur im Rahmen eines Betreuungsvertrags.
                    </p>
                </div>
            </aside>

            <div class="rounded-lg border border-slate-200 p-6 sm:p-10">
                <livewire:site.inquiry-form :preset="is_string(request()->query('anfrage')) ? request()->query('anfrage') : null" />
            </div>
        </div>
    </section>
@endsection
