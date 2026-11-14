@extends('site.layouts.app')

@section('title', 'RuddatTech – Softwarewartung & Managed Application Services für Unternehmen')
@section('meta_description', 'RuddatTech übernimmt Wartung, Stabilisierung und langfristige technische Betreuung individueller Geschäftsanwendungen – von der Laravel-Plattform bis zum komplexen Webportal.')

@push('structured_data')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => route('home') . '#webpage',
            'url' => route('home'),
            'name' => 'RuddatTech – Softwarewartung & Managed Application Services für Unternehmen',
            'inLanguage' => 'de-DE',
            'isPartOf' => ['@id' => \App\Support\Site\StructuredData::websiteId()],
            'about' => ['@id' => \App\Support\Site\StructuredData::organizationId()],
            'primaryImageOfPage' => asset('brand/og-image.png'),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-navy-900 text-white">
        <div class="pointer-events-none absolute inset-0 opacity-[0.07]"
            style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 56px 56px; mask-image: radial-gradient(ellipse at 70% 30%, #000 10%, transparent 70%);"
            aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-brass-400/40 to-transparent" aria-hidden="true"></div>

        <div class="site-container relative grid gap-14 pb-20 pt-32 sm:pb-24 lg:grid-cols-[1.15fr_0.85fr] lg:items-center lg:pb-32 lg:pt-44">
            <div>
                <p class="eyebrow-dark">Softwarewartung &amp; Managed Application Services</p>
                <h1 class="mt-5 text-4xl font-semibold leading-[1.1] tracking-tight sm:text-5xl lg:text-[3.4rem]" style="text-wrap: balance;">
                    Ihre Software ist geschäftskritisch. Ihr Betrieb sollte keine Glückssache sein.
                </h1>
                <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300">
                    RuddatTech übernimmt die technische Wartung, Stabilisierung und langfristige Betreuung individueller
                    Geschäftsanwendungen. Von der bestehenden Laravel-Plattform bis zum komplexen Webportal – mit klaren
                    Verantwortlichkeiten, strukturierten Prozessen und individuell vereinbarten Serviceleistungen.
                </p>
                <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('kontakt') }}" class="btn-primary">
                        Technische Betreuung anfragen
                        @include('site.partials.icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4'])
                    </a>
                    <a href="{{ route('referenzen.index') }}" class="btn-secondary">Referenzprojekte ansehen</a>
                </div>
            </div>

            {{-- Vertragsgegenstand statt Stockfoto: was in einer Betreuung geregelt wird --}}
            <div class="relative">
                <div class="rounded-lg border border-white/10 bg-navy-950/60 p-6 shadow-2xl shadow-black/30 sm:p-8">
                    <div class="flex items-center justify-between border-b border-white/10 pb-5">
                        <p class="text-sm font-semibold text-white">Was in einer Betreuung geregelt wird</p>
                        @include('site.partials.icon', ['name' => 'shield', 'class' => 'h-5 w-5 text-brass-300'])
                    </div>
                    <dl class="divide-y divide-white/10">
                        @foreach ([
                            ['Zuständigkeiten', 'Wer verantwortet Anwendung, Server und Daten?'],
                            ['Update-Rhythmus', 'Wann und wie werden Abhängigkeiten aktualisiert?'],
                            ['Backups', 'Was wird gesichert – und ist die Wiederherstellung getestet?'],
                            ['Deployment', 'Wie gelangen Änderungen nachvollziehbar in den Betrieb?'],
                            ['Kommunikation', 'Welche Wege und Reaktionszeiten gelten im Störungsfall?'],
                        ] as [$term, $desc])
                            <div class="flex gap-4 py-4">
                                <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-brass-400/50 text-brass-300">
                                    @include('site.partials.icon', ['name' => 'check', 'class' => 'h-3 w-3'])
                                </span>
                                <div>
                                    <dt class="text-sm font-semibold text-white">{{ $term }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-slate-400">{{ $desc }}</dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </div>
    </section>

    {{-- Leistungsübersicht --}}
    <section class="bg-white py-20 sm:py-28" aria-labelledby="leistungen-title">
        <div class="site-container">
            <div class="max-w-3xl">
                <p class="eyebrow">Leistungen</p>
                <h2 id="leistungen-title" class="section-title">Komplexe Software. Zuverlässig betreut.</h2>
                <p class="section-lead">
                    Vier Leistungsbereiche, die einzeln oder kombiniert vereinbart werden – abgestimmt auf Zustand,
                    Bedeutung und Betriebsumgebung Ihrer Anwendung.
                </p>
            </div>

            <div class="mt-14 grid gap-6 md:grid-cols-2">
                @foreach (\App\Support\Site\SiteContent::services() as $slug => $service)
                    <a href="{{ route('leistungen.show', $slug) }}" class="card card-hover group flex flex-col" data-reveal>
                        <div class="flex items-start justify-between gap-4">
                            <span class="icon-tile">@include('site.partials.icon', ['name' => $service['icon'], 'class' => 'h-5 w-5'])</span>
                            <span class="text-xs font-medium uppercase tracking-[0.16em] text-slate-500">{{ $service['label'] }}</span>
                        </div>
                        <h3 class="mt-6 text-xl font-semibold text-navy-900">{{ $service['title'] }}</h3>
                        <p class="mt-3 flex-1 leading-7 text-anthracite-700">{{ $service['card_text'] }}</p>
                        <span class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-navy-900">
                            Mehr erfahren
                            <span class="transition group-hover:translate-x-1">@include('site.partials.icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4 text-brass-700'])</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Warum RuddatTech --}}
    <section class="bg-navy-900 py-20 text-white sm:py-28" aria-labelledby="warum-title">
        <div class="site-container">
            <div class="grid gap-14 lg:grid-cols-[0.8fr_1.2fr]">
                <div>
                    <p class="eyebrow-dark">Warum RuddatTech</p>
                    <h2 id="warum-title" class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl" style="text-wrap: balance;">
                        Technische Verantwortung braucht Erfahrung mit echten Systemen.
                    </h2>
                    <p class="mt-5 text-lg leading-8 text-slate-300">
                        Ich entwickle und betreibe selbst umfangreiche Geschäftsanwendungen. Diese Praxis bringe ich in die
                        Wartung und Betreuung Ihrer Software ein.
                    </p>
                </div>

                <div class="grid gap-x-10 gap-y-10 sm:grid-cols-2">
                    @foreach (\App\Support\Site\SiteContent::reasons() as $reason)
                        <div data-reveal>
                            <span class="flex h-10 w-10 items-center justify-center rounded-md border border-white/10 bg-white/5 text-brass-300">
                                @include('site.partials.icon', ['name' => $reason['icon'], 'class' => 'h-5 w-5'])
                            </span>
                            <h3 class="mt-4 text-base font-semibold text-white">{{ $reason['title'] }}</h3>
                            <p class="mt-2 text-[0.95rem] leading-7 text-slate-400">{{ $reason['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Ausgewählte Referenzprojekte (nur veröffentlichte, hervorgehobene Einträge) --}}
    @if ($featuredReferences->isNotEmpty())
        <section class="bg-white py-20 sm:py-28" aria-labelledby="referenzen-title">
            <div class="site-container">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                    <div class="max-w-3xl">
                        <p class="eyebrow">Referenzen</p>
                        <h2 id="referenzen-title" class="section-title">Ausgewählte Referenzprojekte</h2>
                        <p class="section-lead">Technische Fallstudien aus eigener Entwicklung und eigenem Betrieb.</p>
                    </div>
                    <a href="{{ route('referenzen.index') }}" class="btn-outline shrink-0">Alle Referenzen</a>
                </div>

                <div class="mt-14 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featuredReferences as $reference)
                        @include('site.references.card', ['reference' => $reference])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('site.partials.responsibility')

    {{-- Ablauf --}}
    <section class="bg-white py-20 sm:py-28" aria-labelledby="ablauf-title">
        <div class="site-container">
            <div class="max-w-3xl">
                <p class="eyebrow">Zusammenarbeit</p>
                <h2 id="ablauf-title" class="section-title">So läuft eine Zusammenarbeit ab</h2>
                <p class="section-lead">
                    Bevor ich Verantwortung für eine Anwendung übernehme, verschaffen wir uns gemeinsam ein klares Bild.
                </p>
            </div>

            <ol class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-5">
                @foreach (\App\Support\Site\SiteContent::process() as $step)
                    <li class="relative border-t-2 border-navy-900 pt-6" data-reveal>
                        <span class="text-sm font-semibold text-brass-700">Schritt {{ $loop->iteration }}</span>
                        <h3 class="mt-2 text-base font-semibold leading-6 text-navy-900">{{ $step['title'] }}</h3>
                        <p class="mt-3 text-sm leading-6 text-anthracite-700">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    @include('site.partials.cta')
@endsection
