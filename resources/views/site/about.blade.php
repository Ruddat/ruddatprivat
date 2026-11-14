@extends('site.layouts.app')

@section('title', 'Über RuddatTech – persönliche technische Betreuung | RuddatTech')
@section('meta_description', 'RuddatTech ist der technische Dienstleister von Ingo Ruddat: Wartung und Betreuung individueller Geschäftsanwendungen mit direktem Ansprechpartner und nachvollziehbarer Arbeitsweise.')

@push('structured_data')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'AboutPage',
                    '@id' => route('about') . '#webpage',
                    'url' => route('about'),
                    'name' => 'Über RuddatTech',
                    'inLanguage' => 'de-DE',
                    'isPartOf' => ['@id' => \App\Support\Site\StructuredData::websiteId()],
                    'about' => ['@id' => \App\Support\Site\StructuredData::organizationId()],
                    'mainEntity' => ['@id' => \App\Support\Site\StructuredData::founderId()],
                ],
                \App\Support\Site\StructuredData::breadcrumbs(['Startseite' => route('home'), 'Über RuddatTech' => route('about')]),
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    @include('site.partials.page-hero', [
        'eyebrow' => 'Über RuddatTech',
        'title' => 'Ein direkter Ansprechpartner für Ihre Anwendung.',
        'lead' => 'RuddatTech ist der technische Dienstleister von Ingo Ruddat. Der Schwerpunkt liegt auf der Wartung, Stabilisierung und langfristigen Betreuung individueller Webanwendungen, die in Unternehmen produktiv genutzt werden.',
        'breadcrumbs' => ['Startseite' => route('home'), 'Über RuddatTech' => null],
    ])

    <section class="bg-white py-20 sm:py-24">
        <div class="site-container grid gap-14 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="prose-site">
                <h2 class="!mt-0">Aus der Entwicklung in die Verantwortung für den Betrieb</h2>
                <p>
                    Ich entwickle seit Jahren eigene, umfangreiche Webanwendungen – Plattformen mit vielen Datenmodellen,
                    Bestell- und Abrechnungsprozessen, Hintergrundverarbeitung, Schnittstellen zu externen Diensten und
                    Bereichen für unterschiedliche Benutzerrollen. Einige dieser Systeme betreibe ich produktiv.
                </p>
                <p>
                    Dabei habe ich gelernt, dass die eigentliche Arbeit nach dem Go-live beginnt: Updates, Sicherheitslücken,
                    Datenwachstum, Serverwechsel, fehlgeschlagene Hintergrundjobs und Altlasten aus früheren Versionen.
                    Genau diese Erfahrung biete ich Unternehmen an, deren Software zuverlässig funktionieren muss.
                </p>

                <h2>Für wen ich arbeite</h2>
                <p>
                    Für Unternehmen und Betreiber produktiver Software – etwa Portale, Kundenbereiche, Bestell- oder
                    Verwaltungssysteme –, die einen verlässlichen technischen Partner suchen. Kein Massengeschäft und kein
                    Wartungsdienst für beliebige Webseiten, sondern eine überschaubare Zahl langfristiger Betreuungen.
                </p>

                <h2>Technischer Schwerpunkt</h2>
                <p>
                    PHP und Laravel, relationale Datenbanken wie MySQL, Livewire- und Blade-Oberflächen, Queue-Verarbeitung,
                    geplante Aufgaben, E-Mail-Versand, Zahlungs- und API-Anbindungen sowie Linux-Server mit Plesk.
                    Ebenso die Modernisierung älterer PHP-Anwendungen auf aktuelle Versionen.
                </p>
            </div>

            <aside class="h-fit rounded-lg bg-navy-900 p-8 text-white">
                <h2 class="text-lg font-semibold">Grundsätze meiner Arbeit</h2>
                <ul class="check-list check-list-dark mt-6 space-y-4 text-[0.95rem] leading-7 text-slate-300">
                    <li>Erst verstehen, dann ändern – keine Eingriffe ohne Bestandsaufnahme.</li>
                    <li>Änderungen an produktiven Systemen nur nach Abstimmung.</li>
                    <li>Versionierte, dokumentierte und nach Möglichkeit getestete Änderungen.</li>
                    <li>Ehrliche Einschätzungen – auch wenn die Antwort „Ablösung statt Wartung“ lautet.</li>
                    <li>Keine Versprechen, die sich nicht verlässlich einhalten lassen.</li>
                </ul>
                <a href="{{ route('kontakt') }}" class="btn-primary mt-8 w-full">Kontakt aufnehmen</a>
            </aside>
        </div>
    </section>

    @include('site.partials.responsibility')
    @include('site.partials.cta')
@endsection
