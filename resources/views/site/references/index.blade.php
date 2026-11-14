@extends('site.layouts.app')

@section('title', 'Referenzen – technische Fallstudien | RuddatTech')
@section('meta_description', 'Technische Fallstudien von RuddatTech: umfangreiche Webanwendungen, Plattformen und Modernisierungen – mit Ausgangslage, Architektur und Betriebsaspekten.')

@section('content')
    @include('site.partials.page-hero', [
        'eyebrow' => 'Referenzen',
        'title' => 'Technische Fallstudien statt Produktwerbung.',
        'lead' => 'Die folgenden Projekte zeigen, welche Komplexität ich in Entwicklung und Betrieb bewältigt habe – mit Ausgangslage, Architektur, Integrationen und meiner tatsächlichen Rolle.',
        'breadcrumbs' => ['Startseite' => route('home'), 'Referenzen' => null],
    ])

    <section class="bg-white py-20 sm:py-24">
        <div class="site-container">
            @if ($references->isEmpty())
                <div class="mx-auto max-w-2xl rounded-lg border border-dashed border-slate-300 p-10 text-center">
                    <span class="mx-auto icon-tile">@include('site.partials.icon', ['name' => 'document', 'class' => 'h-5 w-5'])</span>
                    <h2 class="mt-5 text-xl font-semibold text-navy-900">Die Fallstudien werden derzeit aufbereitet.</h2>
                    <p class="mt-3 leading-7 text-anthracite-700">
                        Gerne stelle ich Ihnen ausgewählte Projekte im persönlichen Gespräch vor.
                    </p>
                    <a href="{{ route('kontakt') }}" class="btn-outline mt-6">Gespräch vereinbaren</a>
                </div>
            @else
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($references as $reference)
                        @include('site.references.card', ['reference' => $reference])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @include('site.partials.cta', [
        'title' => 'Ihre Anwendung ist ähnlich umfangreich?',
        'text' => 'Dann sprechen wir darüber, wie eine verlässliche technische Betreuung aussehen kann.',
    ])
@endsection
