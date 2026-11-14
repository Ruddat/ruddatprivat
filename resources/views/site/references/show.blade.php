@extends('site.layouts.app')

{{-- Vorläufige Detailansicht. Die vollständige Fallstudien-Struktur (Ausgangslage, Architektur,
     Integrationen, Rolle, Galerie mit Alt-Texten) folgt mit der Erweiterung der Portfolio-Verwaltung. --}}

@section('title', $reference->title . ' – Referenz | RuddatTech')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($reference->summary ?: $reference->description), 155))
@section('og_type', 'article')

@section('content')
    @include('site.partials.page-hero', [
        'eyebrow' => $reference->category ?: 'Referenz',
        'title' => $reference->title,
        'lead' => $reference->summary,
        'breadcrumbs' => ['Startseite' => route('home'), 'Referenzen' => route('referenzen.index'), $reference->title => null],
    ])

    <section class="bg-white py-16 sm:py-20">
        <div class="site-container grid gap-12 lg:grid-cols-[1fr_320px]">
            <div class="prose-site">
                {!! nl2br(e($reference->description)) !!}
            </div>

            @if (!empty($reference->badges))
                <aside class="h-fit rounded-lg border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500">Technologien</h2>
                    <ul class="mt-4 flex flex-wrap gap-2">
                        @foreach ($reference->badges as $badge)
                            <li class="rounded-md border border-slate-200 bg-white px-2.5 py-1 text-sm text-anthracite-800">{{ $badge }}</li>
                        @endforeach
                    </ul>
                </aside>
            @endif
        </div>

        @if ($reference->images->isNotEmpty())
            <div class="site-container mt-16">
                <h2 class="text-2xl font-semibold tracking-tight text-navy-900">Projektansichten</h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($reference->images as $image)
                        <figure class="overflow-hidden rounded-lg border border-slate-200">
                            <img src="{{ \App\Models\PortfolioItem::imageUrl($image->path) }}" alt="{{ $image->caption ?: 'Ansicht ' . $loop->iteration . ' von ' . $reference->title }}"
                                loading="lazy" decoding="async" class="aspect-[16/10] w-full object-cover object-top">
                            @if ($image->caption)
                                <figcaption class="px-4 py-3 text-sm text-anthracite-700">{{ $image->caption }}</figcaption>
                            @endif
                        </figure>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    @include('site.partials.cta')
@endsection
