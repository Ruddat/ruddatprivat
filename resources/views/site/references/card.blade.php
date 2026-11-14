{{-- Übersichtskarte einer Referenz (einheitliches Seitenverhältnis 16:10) --}}
<article class="group relative flex flex-col overflow-hidden rounded-lg border border-slate-200 bg-white transition duration-200 hover:-translate-y-0.5 hover:border-brass-300 hover:shadow-[0_16px_40px_-20px_rgba(11,20,38,0.35)]" data-reveal>
    <a href="{{ route('referenzen.show', $reference) }}" class="block aspect-[16/10] overflow-hidden bg-slate-100" tabindex="-1" aria-hidden="true">
        @if ($reference->cover_url)
            <img src="{{ $reference->cover_url }}" alt="" loading="lazy" decoding="async"
                @if ($reference->cover_srcset) srcset="{{ $reference->cover_srcset }}" sizes="(min-width: 1024px) 400px, (min-width: 768px) 50vw, 100vw" @endif
                class="h-full w-full object-cover object-top transition duration-500 group-hover:scale-[1.02]">
        @else
            <div class="flex h-full w-full items-center justify-center bg-navy-900 text-brass-300">
                @include('site.partials.icon', ['name' => 'layers', 'class' => 'h-10 w-10'])
            </div>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-6">
        @if ($reference->category)
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-brass-700">{{ $reference->category }}</p>
        @endif
        <h3 class="mt-2 text-lg font-semibold text-navy-900">
            <a href="{{ route('referenzen.show', $reference) }}" class="after:absolute after:inset-0 focus:outline-none">{{ $reference->title }}</a>
        </h3>
        @if ($reference->summary)
            <p class="mt-3 line-clamp-3 flex-1 text-[0.95rem] leading-7 text-anthracite-700">{{ $reference->summary }}</p>
        @endif
        <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-navy-900">
            Fallstudie ansehen
            @include('site.partials.icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4 text-brass-700'])
        </span>
    </div>
</article>
