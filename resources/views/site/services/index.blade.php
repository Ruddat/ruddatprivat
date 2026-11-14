@extends('site.layouts.app')

@section('title', 'Leistungen – Softwarewartung, Betrieb und Übernahme | RuddatTech')
@section('meta_description', 'Softwarewartung, Managed Application Services, technische Übernahme bestehender Anwendungen sowie Fehleranalyse und Stabilisierung – individuell vereinbart.')

@section('content')
    @include('site.partials.page-hero', [
        'eyebrow' => 'Leistungen',
        'title' => 'Technische Betreuung für Anwendungen, die zuverlässig funktionieren müssen.',
        'lead' => 'Ob laufende Wartung, Verantwortung für den Betrieb, Übernahme einer verwaisten Anwendung oder Hilfe bei einer akuten Störung – jede Leistung wird auf Ihre Anwendung zugeschnitten.',
        'breadcrumbs' => ['Startseite' => route('home'), 'Leistungen' => null],
    ])

    <section class="bg-white py-20 sm:py-24">
        <div class="site-container space-y-6">
            @foreach ($services as $slug => $service)
                <a href="{{ route('leistungen.show', $slug) }}"
                    class="card card-hover group grid gap-6 md:grid-cols-[auto_1fr_auto] md:items-center md:gap-10" data-reveal>
                    <span class="icon-tile h-14 w-14">@include('site.partials.icon', ['name' => $service['icon'], 'class' => 'h-6 w-6'])</span>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.16em] text-slate-500">{{ $service['label'] }}</p>
                        <h2 class="mt-1 text-xl font-semibold text-navy-900 sm:text-2xl">{{ $service['title'] }}</h2>
                        <p class="mt-3 max-w-3xl leading-7 text-anthracite-700">{{ $service['card_text'] }}</p>
                    </div>
                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-navy-900">
                        Details
                        <span class="transition group-hover:translate-x-1">@include('site.partials.icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4 text-brass-700'])</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    @include('site.partials.responsibility')
    @include('site.partials.cta')
@endsection
