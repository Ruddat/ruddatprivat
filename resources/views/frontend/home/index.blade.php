@extends('frontend.layouts.app')

@section('title', 'Ingo Ruddat – Laravel Entwickler, Webprodukte & digitale Systeme')
@section('meta_description', 'Ingo Ruddat entwickelt Laravel- und Livewire-Webanwendungen, Landingpages, Automatisierungen, Adminsysteme, SEO-Strukturen und digitale Geschäftsprozesse.')
@section('meta_keywords', 'Laravel Entwickler, Livewire, Webentwicklung, Automatisierung, SEO Landingpages, SaaS, Adminsysteme, Peine, Braunschweig, Hannover')

@section('content')
    @php
        $appointmentUrl = \Illuminate\Support\Facades\Route::has('schedule.appointment')
            ? route('schedule.appointment')
            : url('/#contact');
    @endphp

    <section id="hero" class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(236,72,153,0.32),transparent_34%),radial-gradient(circle_at_80%_20%,rgba(59,130,246,0.24),transparent_32%),linear-gradient(135deg,#020617_0%,#111827_52%,#18181b_100%)]"></div>
        <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-white to-transparent"></div>

        <div class="relative mx-auto max-w-7xl px-6 pb-24 pt-36 lg:px-8 lg:pb-32 lg:pt-44">
            <div class="grid items-center gap-14 lg:grid-cols-[1.05fr_0.95fr]">
                <div>
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm text-white/80 shadow-xl shadow-black/20 backdrop-blur">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        Laravel · Livewire · SEO · Automatisierung · KI-Workflows
                    </div>

                    <h1 class="max-w-4xl text-4xl font-black tracking-tight sm:text-6xl lg:text-7xl">
                        Ich baue digitale Systeme, die nicht nur gut aussehen – sondern arbeiten.
                    </h1>

                    <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300 sm:text-xl">
                        Webanwendungen, Kundenportale, Landingpage-Systeme, Adminbereiche, Zahlungsprozesse und Automatisierungen. Sauber geplant, pragmatisch gebaut und auf echte Nutzung ausgelegt.
                    </p>

                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ $appointmentUrl }}" class="inline-flex items-center justify-center rounded-full bg-pink-500 px-7 py-4 text-sm font-bold text-white shadow-lg shadow-pink-500/30 transition hover:-translate-y-0.5 hover:bg-pink-400">
                            Projekt besprechen
                        </a>
                        <a href="{{ url('/#work') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/10 px-7 py-4 text-sm font-bold text-white backdrop-blur transition hover:-translate-y-0.5 hover:bg-white/15">
                            Beispiele ansehen
                        </a>
                    </div>

                    <div class="mt-10 grid max-w-2xl grid-cols-3 gap-4 border-t border-white/10 pt-7 text-sm text-slate-300">
                        <div>
                            <strong class="block text-2xl text-white">Laravel</strong>
                            Produktentwicklung
                        </div>
                        <div>
                            <strong class="block text-2xl text-white">SEO</strong>
                            Landingpage-Strukturen
                        </div>
                        <div>
                            <strong class="block text-2xl text-white">AI</strong>
                            Workflows & Analyse
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-8 rounded-[3rem] bg-pink-500/20 blur-3xl"></div>
                    <div class="relative rounded-[2rem] border border-white/10 bg-white/10 p-4 shadow-2xl shadow-black/40 backdrop-blur-xl">
                        <div class="rounded-[1.5rem] bg-slate-950/90 p-5 ring-1 ring-white/10">
                            <div class="flex items-center gap-2 border-b border-white/10 pb-4">
                                <span class="h-3 w-3 rounded-full bg-red-400"></span>
                                <span class="h-3 w-3 rounded-full bg-yellow-400"></span>
                                <span class="h-3 w-3 rounded-full bg-green-400"></span>
                                <span class="ml-3 text-xs text-slate-400">ruddat.systems/build</span>
                            </div>

                            <div class="mt-5 space-y-4 font-mono text-sm">
                                <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-emerald-200">
                                    ✓ Analyse: Geschäftsprozess, Zielgruppe, Conversion
                                </div>
                                <div class="rounded-2xl border border-blue-400/20 bg-blue-400/10 p-4 text-blue-200">
                                    ✓ Umsetzung: Laravel, Livewire, Datenmodell, Admin UX
                                </div>
                                <div class="rounded-2xl border border-pink-400/20 bg-pink-400/10 p-4 text-pink-200">
                                    ✓ Wachstum: SEO-Landings, Automatisierung, Tracking
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-slate-300">
                                    deploy --ready-for-real-users
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="bg-white py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:items-start">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.3em] text-pink-600">Profil</p>
                    <h2 class="mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-5xl">
                        Entwickler mit Produktblick statt nur Code-Ausgabe.
                    </h2>
                    <p class="mt-6 text-lg leading-8 text-slate-600">
                        Ich denke Anwendungen nicht nur als Oberfläche, sondern als System: Datenmodell, Rechte, Prozesse, Zahlungslogik, E-Mail-Flows, Admin-Bedienung, SEO, Tracking und Betrieb. Genau dort entstehen stabile Lösungen.
                    </p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <h3 class="text-lg font-bold text-slate-950">Webprodukte & SaaS</h3>
                        <p class="mt-3 text-slate-600">Von der ersten Landingpage bis zum geschützten Kundenbereich mit Rollen, Limits, Zahlungen und Workflows.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <h3 class="text-lg font-bold text-slate-950">Laravel & Livewire</h3>
                        <p class="mt-3 text-slate-600">Schnelle, robuste Backends und interaktive Oberflächen ohne unnötige Komplexität.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <h3 class="text-lg font-bold text-slate-950">SEO-Landingpages</h3>
                        <p class="mt-3 text-slate-600">Strukturierte Seitenkonzepte für Städte, Leistungen, Kategorien und Suchintentionen.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <h3 class="text-lg font-bold text-slate-950">Automatisierung & KI</h3>
                        <p class="mt-3 text-slate-600">Dokumentanalyse, Antwortentwürfe, Bildprozesse, Statusjobs, Reports und interne Werkzeuge.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="bg-slate-50 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-pink-600">Leistungen</p>
                <h2 class="mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-5xl">Was ich für Kunden und eigene Projekte baue.</h2>
            </div>

            <div class="mt-12 grid gap-6 lg:grid-cols-3">
                @foreach([
                    ['title' => 'Laravel-Anwendungen', 'text' => 'Dashboards, Kundenbereiche, Adminmodule, Buchungsstrecken, Workflows und individuelle Datenmodelle.'],
                    ['title' => 'Landingpage-Systeme', 'text' => 'Skalierbare SEO-Seiten für Orte, Leistungen, Kategorien, Anbieter und Spezialseiten.'],
                    ['title' => 'Payment & E-Mail-Prozesse', 'text' => 'PayPal, Rechnungslogik, Statusmails, Bestellbestätigungen, Magic-Links und sichere Versandprozesse.'],
                    ['title' => 'AI-gestützte Features', 'text' => 'Dokumentenprüfung, Zusammenfassungen, Antwortvorschläge, Bildgenerierung und interne Assistenzfunktionen.'],
                    ['title' => 'Admin UX & Reports', 'text' => 'Übersichten, Health-Checks, Exportfunktionen, Filter, Audit-Logs und operative Werkzeuge.'],
                    ['title' => 'Technischer Feinschliff', 'text' => 'Performance, Fehlerseiten, Sitemaps, Tracking, Crawlbarkeit, Deployments und Go-Live-Checks.'],
                ] as $service)
                    <article class="group rounded-3xl border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                        <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-2xl bg-pink-50 text-xl font-black text-pink-600">{{ $loop->iteration }}</div>
                        <h3 class="text-xl font-black text-slate-950">{{ $service['title'] }}</h3>
                        <p class="mt-4 leading-7 text-slate-600">{{ $service['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="work" class="bg-white py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.3em] text-pink-600">Projekte</p>
                    <h2 class="mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-5xl">Ich arbeite am liebsten an Systemen mit echter Funktion.</h2>
                    <p class="mt-6 text-lg leading-8 text-slate-600">
                        Beispiele aus meinem Umfeld: Lieferplattformen, Fristen- und Dokumentenapps, regionale Landingpage-Netzwerke, Partyverleih-Seiten, Sammlungsportale und interne Verwaltungswerkzeuge.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach([
                        'Bestell- und Lieferplattformen',
                        'Fristen- und Dokumentenanalyse',
                        'Lokale SEO-Landingpages',
                        'Portfolio- und Sammlungsseiten',
                        'Widget-Systeme für Spezialseiten',
                        'Backoffice- und Reportingmodule',
                    ] as $item)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 font-semibold text-slate-800">
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-14">
                @livewire('frontend.portfolio-grid')
            </div>
        </div>
    </section>

    <section id="stack" class="bg-slate-950 py-24 text-white">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-[0.8fr_1.2fr]">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.3em] text-pink-300">Stack</p>
                    <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-5xl">Technik, die ich produktiv einsetze.</h2>
                    <p class="mt-6 leading-8 text-slate-300">Kein Buzzword-Bingo. Ich nutze Werkzeuge, die schnelle Entwicklung, stabile Datenlogik und wartbare Systeme ermöglichen.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @foreach(['Laravel', 'Livewire', 'PHP', 'MySQL', 'Tailwind CSS', 'Blade', 'Vite', 'PayPal', 'OpenRouter', 'Jobs & Queues', 'PDF-Generierung', 'Excel-Exports', 'SEO', 'Sitemaps', 'Analytics', 'Plesk / Hosting'] as $tech)
                        <span class="rounded-full border border-white/10 bg-white/10 px-5 py-3 text-sm font-semibold text-slate-100 shadow-lg shadow-black/20">{{ $tech }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="process" class="bg-white py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-pink-600">Ablauf</p>
                <h2 class="mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-5xl">So wird aus einer Idee ein nutzbares System.</h2>
            </div>

            <div class="mt-12 grid gap-6 lg:grid-cols-4">
                @foreach([
                    ['Analyse', 'Ziel, Nutzer, Daten, Risiken und Suchintention sauber klären.'],
                    ['Struktur', 'Routen, Datenmodell, Komponenten, Adminbereich und Rechte festlegen.'],
                    ['Umsetzung', 'In kurzen, prüfbaren Schritten bauen und direkt nutzbar machen.'],
                    ['Optimierung', 'SEO, UX, Tracking, Stabilität und Go-Live-Checks nachziehen.'],
                ] as [$title, $text])
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-7">
                        <div class="mb-8 text-sm font-black uppercase tracking-[0.25em] text-pink-600">0{{ $loop->iteration }}</div>
                        <h3 class="text-xl font-black text-slate-950">{{ $title }}</h3>
                        <p class="mt-4 leading-7 text-slate-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="contact" class="bg-slate-50 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                <div class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-2xl">
                    <p class="text-sm font-bold uppercase tracking-[0.3em] text-pink-300">Kontakt</p>
                    <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-5xl">Lass uns dein nächstes System sauber aufsetzen.</h2>
                    <p class="mt-6 leading-8 text-slate-300">
                        Schick mir kurz, was gebaut werden soll: neue Webseite, Laravel-App, Kundenportal, Landingpage-System, Adminbereich oder Optimierung eines bestehenden Projekts.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 text-sm text-slate-300">
                        <span>✓ Klare technische Einschätzung</span>
                        <span>✓ Pragmatischer Umsetzungsplan</span>
                        <span>✓ Fokus auf echten Nutzen statt Deko</span>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <livewire:frontend.contact-form.contact-form-component />
                </div>
            </div>
        </div>
    </section>
@endsection
