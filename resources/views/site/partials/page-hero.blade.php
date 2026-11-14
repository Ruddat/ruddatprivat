{{-- Kopfbereich für Unterseiten. Parameter: $eyebrow, $title, $lead, $breadcrumbs (array label => url|null) --}}
<section class="relative overflow-hidden bg-navy-900 pb-16 pt-32 text-white sm:pb-20 lg:pt-40">
    <div class="pointer-events-none absolute inset-0 opacity-[0.07]"
        style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 48px 48px; mask-image: linear-gradient(to bottom, #000, transparent 85%);"
        aria-hidden="true"></div>

    <div class="site-container relative">
        @if (!empty($breadcrumbs))
            <nav aria-label="Brotkrümelnavigation" class="mb-8 text-sm text-slate-400">
                <ol class="flex flex-wrap items-center gap-2">
                    @foreach ($breadcrumbs as $label => $url)
                        <li class="flex items-center gap-2">
                            @if ($url)
                                <a href="{{ $url }}" class="transition hover:text-white">{{ $label }}</a>
                                <span aria-hidden="true" class="text-slate-600">/</span>
                            @else
                                <span aria-current="page" class="text-slate-200">{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        @isset($eyebrow)
            <p class="eyebrow-dark">{{ $eyebrow }}</p>
        @endisset
        <h1 class="mt-4 max-w-4xl text-4xl font-semibold tracking-tight sm:text-5xl" style="text-wrap: balance;">
            {{ $title }}
        </h1>
        @isset($lead)
            <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-300">{{ $lead }}</p>
        @endisset
    </div>
</section>
