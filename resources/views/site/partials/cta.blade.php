{{-- Abschluss-CTA --}}
<section class="bg-white py-20 sm:py-24">
    <div class="site-container">
        <div class="relative overflow-hidden rounded-lg bg-navy-900 px-6 py-12 text-white sm:px-12 sm:py-16 lg:flex lg:items-center lg:justify-between lg:gap-12">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full border border-brass-400/20" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -right-8 -top-8 h-40 w-40 rounded-full border border-brass-400/20" aria-hidden="true"></div>

            <div class="relative max-w-2xl">
                <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl" style="text-wrap: balance;">
                    {{ $title ?? 'Sie betreiben eine wichtige Webanwendung und suchen einen langfristigen technischen Partner?' }}
                </h2>
                <p class="mt-4 text-base leading-7 text-slate-300">
                    {{ $text ?? 'Lassen Sie uns über Ihre Anforderungen sprechen.' }}
                </p>
            </div>
            <div class="relative mt-8 shrink-0 lg:mt-0">
                <a href="{{ route('kontakt') }}" class="btn-primary">
                    {{ $button ?? 'Betreuung anfragen' }}
                    @include('site.partials.icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4'])
                </a>
            </div>
        </div>
    </div>
</section>
