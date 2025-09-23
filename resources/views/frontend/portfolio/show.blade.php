{{-- resources/views/frontend/portfolio/show.blade.php --}}
@extends("frontend.layouts.app")

@section("title", $portfolioItem->title)

@section("content")

    @include("frontend.home.sections.hero")

    <section class="py-16 bg-base-200">
        <div class="max-w-5xl mx-auto px-4">
            <h1 class="text-4xl font-bold mb-6">{{ $portfolioItem->title }}</h1>
            <p class="text-base-content/70 mb-8">{{ $portfolioItem->summary }}</p>

            <!-- Galerie -->
            <div class="grid sm:grid-cols-2 gap-4 mb-8">
                <a href="{{ Storage::url($portfolioItem->cover_image) }}" class="glightbox"
                    data-gallery="gallery-{{ $portfolioItem->id }}">
                    <img src="{{ Storage::url($portfolioItem->cover_image) }}"
                        class="rounded-lg shadow-lg">
                </a>
                @foreach ($portfolioItem->images as $img)
                    <a href="{{ Storage::url($img->path) }}" class="glightbox"
                        data-gallery="gallery-{{ $portfolioItem->id }}">
                        <img src="{{ Storage::url($img->path) }}" class="rounded-lg shadow-lg">
                    </a>
                @endforeach
            </div>

            <!-- Lange Beschreibung -->
            <div class="prose max-w-none">
                {!! nl2br(e($portfolioItem->description)) !!}
            </div>

            <!-- Badges -->
            <div class="mt-6 flex flex-wrap gap-2">
                @foreach ($portfolioItem->badges ?? [] as $badge)
                    <span class="badge badge-primary">{{ $badge }}</span>
                @endforeach
            </div>
        </div>
    </section>
@endsection
