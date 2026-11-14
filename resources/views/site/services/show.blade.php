@extends('site.layouts.app')

@section('title', $service['seo_title'])
@section('meta_description', $service['meta_description'])

@push('structured_data')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Service',
                    'name' => $service['title'],
                    'serviceType' => $service['label'],
                    'description' => $service['meta_description'],
                    'url' => route('leistungen.show', $service['slug']),
                    'provider' => ['@type' => 'ProfessionalService', 'name' => 'RuddatTech', 'url' => route('home')],
                    'areaServed' => ['@type' => 'Country', 'name' => 'Deutschland'],
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Leistungen', 'item' => route('leistungen.index')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $service['title'], 'item' => route('leistungen.show', $service['slug'])],
                    ],
                ],
                [
                    '@type' => 'FAQPage',
                    'mainEntity' => collect($service['faq'])->map(fn ($f) => [
                        '@type' => 'Question',
                        'name' => $f['q'],
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
                    ])->all(),
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    @include('site.partials.page-hero', [
        'eyebrow' => $service['label'],
        'title' => $service['title'],
        'lead' => $service['intro'],
        'breadcrumbs' => ['Startseite' => route('home'), 'Leistungen' => route('leistungen.index'), $service['title'] => null],
    ])

    {{-- Ausgangslage --}}
    <section class="bg-white py-20 sm:py-24">
        <div class="site-container grid gap-12 lg:grid-cols-[0.9fr_1.1fr]">
            <div>
                <p class="eyebrow">Ausgangslage</p>
                <h2 class="section-title">{{ $service['headline'] }}</h2>
            </div>
            <div>
                <p class="text-base font-medium text-navy-900">Typische Situationen, in denen Unternehmen auf mich zukommen:</p>
                <ul class="check-list mt-6 space-y-4 text-anthracite-700">
                    @foreach ($service['situation'] as $item)
                        <li class="leading-7">{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- Leistungsumfang --}}
    <section class="bg-slate-50 py-20 sm:py-24" aria-labelledby="umfang-title">
        <div class="site-container">
            <div class="max-w-3xl">
                <p class="eyebrow">Leistungsumfang</p>
                <h2 id="umfang-title" class="section-title">Was konkret dazugehört</h2>
                <p class="section-lead">Welche Bausteine Sie benötigen, legen wir nach der technischen Bestandsaufnahme gemeinsam fest.</p>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($service['scope'] as $item)
                    <div class="card" data-reveal>
                        <h3 class="text-base font-semibold text-navy-900">{{ $item['title'] }}</h3>
                        <p class="mt-3 text-[0.95rem] leading-7 text-anthracite-700">{{ $item['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Nutzen --}}
    <section class="bg-navy-900 py-20 text-white sm:py-24">
        <div class="site-container grid gap-12 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="eyebrow-dark">Ihr Nutzen</p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Was sich für Sie verändert</h2>
            </div>
            <ul class="check-list check-list-dark space-y-4 text-slate-200">
                @foreach ($service['benefits'] as $benefit)
                    <li class="leading-7">{{ $benefit }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    @include('site.partials.responsibility')

    {{-- Häufige Fragen --}}
    <section class="bg-white py-20 sm:py-24" aria-labelledby="faq-title">
        <div class="site-container max-w-4xl">
            <p class="eyebrow">Häufige Fragen</p>
            <h2 id="faq-title" class="section-title">Gut zu wissen</h2>
            <div class="mt-10 divide-y divide-slate-200 border-y border-slate-200">
                @foreach ($service['faq'] as $faq)
                    <details class="group py-5">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-6 text-base font-semibold text-navy-900 [&::-webkit-details-marker]:hidden">
                            {{ $faq['q'] }}
                            <span class="text-brass-700 transition group-open:rotate-45" aria-hidden="true">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>
                            </span>
                        </summary>
                        <p class="mt-4 max-w-prose leading-7 text-anthracite-700">{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Weitere Leistungen --}}
    <section class="border-t border-slate-200 bg-slate-50 py-16">
        <div class="site-container">
            <h2 class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500">Weitere Leistungen</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                @foreach ($otherServices as $slug => $other)
                    <a href="{{ route('leistungen.show', $slug) }}" class="card card-hover group flex items-center justify-between gap-4 p-5">
                        <span class="font-semibold text-navy-900">{{ $other['title'] }}</span>
                        @include('site.partials.icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4 shrink-0 text-brass-700'])
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @include('site.partials.cta', ['href' => route('kontakt', ['anfrage' => $service['inquiry_type']])])
@endsection
