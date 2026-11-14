@extends('site.layouts.app')

@section('title', 'Seite nicht gefunden | RuddatTech')
@section('robots', 'noindex, follow')

@section('content')
    <section class="flex min-h-[70vh] items-center bg-navy-900 pb-20 pt-32 text-white">
        <div class="site-container">
            <p class="eyebrow-dark">Fehler 404</p>
            <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-tight sm:text-5xl">Diese Seite gibt es nicht (mehr).</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">
                Möglicherweise wurde die Adresse geändert. Die wichtigsten Inhalte finden Sie hier:
            </p>
            <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('home') }}" class="btn-primary">Zur Startseite</a>
                <a href="{{ route('leistungen.index') }}" class="btn-secondary">Leistungen</a>
                <a href="{{ route('kontakt') }}" class="btn-secondary">Kontakt</a>
            </div>
        </div>
    </section>
@endsection
