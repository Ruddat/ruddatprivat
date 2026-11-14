@extends('site.layouts.app')

@section('title', $reference->seo_title ?: $reference->title . ' – Referenz | RuddatTech')
@section('meta_description', $reference->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($reference->summary ?: $reference->description), 160))
@section('og_type', 'article')
@if ($reference->cover_url)
    @section('og_image', url($reference->cover_url))
    @section('og_image_alt', $reference->cover_alt ?: $reference->title)
@endif

@push('structured_data')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => array_values(array_filter([
                [
                    '@type' => 'CreativeWork',
                    'name' => $reference->title,
                    'description' => $reference->summary,
                    'url' => route('referenzen.show', $reference),
                    'image' => $reference->cover_url ? url($reference->cover_url) : null,
                    'keywords' => implode(', ', $reference->badges ?? []),
                    'creator' => ['@id' => \App\Support\Site\StructuredData::organizationId()],
                    'about' => array_values($reference->complexityLabels()),
                    'inLanguage' => 'de-DE',
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Referenzen', 'item' => route('referenzen.index')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $reference->title, 'item' => route('referenzen.show', $reference)],
                    ],
                ],
            ])),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@php
    $facts = array_filter([
        'Projekttyp' => $reference->project_type,
        'Projektstatus' => $reference->project_status,
    ]);
    $sections = array_filter([
        'Ausgangssituation' => $reference->challenge,
        'Technische Herausforderungen' => $reference->technical_challenges,
        'Architektur' => $reference->architecture,
        'Datenbank, Sicherheit und Betrieb' => $reference->operations,
    ]);
    $complexity = $reference->complexityLabels();
@endphp

@section('content')
    @include('site.partials.page-hero', [
        'eyebrow' => $reference->project_type ?: ($reference->category ?: 'Referenz'),
        'title' => $reference->title,
        'lead' => $reference->summary,
        'breadcrumbs' => ['Startseite' => route('home'), 'Referenzen' => route('referenzen.index'), $reference->title => null],
    ])

    @if ($reference->cover_url)
        <div class="bg-navy-900">
            <div class="site-container">
                <figure class="relative -mb-16 overflow-hidden rounded-t-lg border border-b-0 border-white/10 shadow-2xl sm:-mb-24">
                    <img src="{{ $reference->cover_url }}"
                        @if ($reference->cover_srcset) srcset="{{ $reference->cover_srcset }}" sizes="(min-width: 1280px) 1216px, 100vw" @endif
                        alt="{{ $reference->cover_alt ?: 'Ansicht von ' . $reference->title }}"
                        fetchpriority="high" decoding="async" class="aspect-[16/9] w-full object-cover object-top">
                </figure>
            </div>
        </div>
    @endif

    <section class="bg-white pb-20 pt-28 sm:pb-24 sm:pt-36">
        <div class="site-container grid gap-14 lg:grid-cols-[minmax(0,1fr)_340px]">
            <article class="min-w-0">
                @if ($reference->description)
                    <div class="prose-site text-lg">
                        {!! nl2br(e($reference->description)) !!}
                    </div>
                @endif

                @foreach ($sections as $heading => $text)
                    <section class="mt-14 first:mt-0" data-reveal>
                        <h2 class="text-2xl font-semibold tracking-tight text-navy-900">{{ $heading }}</h2>
                        <div class="prose-site mt-4">{!! nl2br(e($text)) !!}</div>
                    </section>
                @endforeach

                @if (!empty($reference->features))
                    <section class="mt-14" data-reveal>
                        <h2 class="text-2xl font-semibold tracking-tight text-navy-900">Wesentliche Funktionen</h2>
                        <ul class="check-list mt-6 grid gap-x-8 gap-y-3 text-anthracite-700 sm:grid-cols-2">
                            @foreach ($reference->features as $feature)
                                <li class="leading-7">{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if (!empty($reference->integrations))
                    <section class="mt-14" data-reveal>
                        <h2 class="text-2xl font-semibold tracking-tight text-navy-900">Integrationen und Schnittstellen</h2>
                        <ul class="mt-6 flex flex-wrap gap-2">
                            @foreach ($reference->integrations as $integration)
                                <li class="rounded-md border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm text-anthracite-800">{{ $integration }}</li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </article>

            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                @if ($facts || $reference->role)
                    <div class="rounded-lg border border-slate-200 p-6">
                        <dl class="space-y-5 text-sm">
                            @foreach ($facts as $label => $value)
                                <div>
                                    <dt class="font-semibold uppercase tracking-[0.14em] text-slate-500 text-xs">{{ $label }}</dt>
                                    <dd class="mt-1 text-base text-navy-900">{{ $value }}</dd>
                                </div>
                            @endforeach
                            @if ($reference->role)
                                <div>
                                    <dt class="font-semibold uppercase tracking-[0.14em] text-slate-500 text-xs">Rolle von RuddatTech</dt>
                                    <dd class="mt-1 leading-6 text-navy-900">{!! nl2br(e($reference->role)) !!}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif

                @if ($complexity)
                    <div class="rounded-lg bg-navy-900 p-6 text-white">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-brass-300">Bewältigte Komplexität</h2>
                        <ul class="check-list check-list-dark mt-4 space-y-2.5 text-sm text-slate-200">
                            @foreach ($complexity as $label)
                                <li>{{ $label }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (!empty($reference->badges))
                    <div class="rounded-lg border border-slate-200 p-6">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Technologien</h2>
                        <ul class="mt-4 flex flex-wrap gap-2">
                            @foreach ($reference->badges as $badge)
                                <li class="rounded-md bg-slate-100 px-2.5 py-1 text-sm text-anthracite-800">{{ $badge }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($reference->cta_link)
                    <a href="{{ $reference->cta_link }}" target="_blank" rel="noopener" class="btn-outline w-full">
                        Projekt ansehen
                        @include('site.partials.icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4'])
                    </a>
                @endif
            </aside>
        </div>
    </section>

    {{-- Screenshot-Galerie mit Vergrößerung --}}
    @if ($reference->images->isNotEmpty())
        <section class="border-t border-slate-200 bg-slate-50 py-20 sm:py-24" aria-labelledby="galerie-title">
            <div class="site-container">
                <h2 id="galerie-title" class="text-2xl font-semibold tracking-tight text-navy-900">Projektansichten</h2>
                <p class="mt-2 text-sm text-slate-500">Zum Vergrößern auf ein Bild klicken.</p>

                <ul class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-gallery>
                    @foreach ($reference->images as $image)
                        @php
                            $alt = $image->alt ?: 'Ansicht ' . $loop->iteration . ' von ' . $reference->title;
                        @endphp
                        <li>
                            <figure>
                                <a href="{{ $image->url }}" data-gallery-item data-caption="{{ $image->caption ?: $alt }}"
                                    class="group block overflow-hidden rounded-lg border border-slate-200 bg-white">
                                    <img src="{{ $image->url }}"
                                        @if ($image->srcset) srcset="{{ $image->srcset }}" sizes="(min-width: 1024px) 400px, (min-width: 640px) 50vw, 100vw" @endif
                                        alt="{{ $alt }}" loading="lazy" decoding="async"
                                        @if ($image->width) width="{{ $image->width }}" height="{{ $image->height }}" @endif
                                        class="aspect-[16/10] w-full object-cover object-top transition duration-300 group-hover:scale-[1.02]">
                                    <span class="sr-only">– vergrößern</span>
                                </a>
                                @if ($image->caption)
                                    <figcaption class="mt-3 text-sm leading-6 text-anthracite-700">{{ $image->caption }}</figcaption>
                                @endif
                            </figure>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <dialog id="lightbox" class="m-auto max-h-[100dvh] max-w-[100vw] bg-transparent p-0 backdrop:bg-navy-950/90" aria-label="Bildansicht">
            <div class="flex max-h-[100dvh] w-screen max-w-6xl flex-col items-center px-4 py-6">
                <div class="mb-3 flex w-full items-center justify-between text-sm text-slate-300">
                    <span data-lightbox-counter></span>
                    <button type="button" data-lightbox-close class="inline-flex h-11 w-11 items-center justify-center rounded-md text-white hover:bg-white/10">
                        <span class="sr-only">Schließen</span>
                        @include('site.partials.icon', ['name' => 'close', 'class' => 'h-6 w-6'])
                    </button>
                </div>
                <div class="relative flex w-full items-center justify-center">
                    <button type="button" data-lightbox-prev class="absolute left-0 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full bg-navy-900/80 text-white hover:bg-navy-800">
                        <span class="sr-only">Vorheriges Bild</span>
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 6-6 6 6 6" /></svg>
                    </button>
                    <img data-lightbox-image src="" alt="" class="max-h-[78dvh] w-auto max-w-full rounded-md object-contain shadow-2xl">
                    <button type="button" data-lightbox-next class="absolute right-0 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full bg-navy-900/80 text-white hover:bg-navy-800">
                        <span class="sr-only">Nächstes Bild</span>
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 6 6 6-6 6" /></svg>
                    </button>
                </div>
                <p data-lightbox-caption class="mt-4 max-w-3xl text-center text-sm text-slate-300"></p>
            </div>
        </dialog>
    @endif

    @include('site.partials.cta', [
        'title' => 'Sie betreiben eine ähnlich komplexe Anwendung?',
        'text' => 'Lassen Sie uns darüber sprechen, wie eine verlässliche Wartung und Betreuung aussehen kann.',
    ])
@endsection
