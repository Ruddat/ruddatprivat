{{-- Abgrenzung: Wartung / Betriebsverantwortung / Verfügbarkeitsleistungen --}}
<section class="bg-slate-50 py-20 sm:py-24" aria-labelledby="abgrenzung-title">
    <div class="site-container">
        <div class="max-w-3xl">
            <p class="eyebrow">Klare Verantwortlichkeiten</p>
            <h2 id="abgrenzung-title" class="section-title">Wartung, Betrieb und Verfügbarkeit – sauber getrennt.</h2>
            <p class="section-lead">
                Damit Sie wissen, wofür Sie sich auf wen verlassen können, wird jede Leistungsebene einzeln vereinbart.
            </p>
        </div>

        <div class="mt-12 grid gap-px overflow-hidden rounded-lg border border-slate-200 bg-slate-200 md:grid-cols-3">
            @foreach (\App\Support\Site\SiteContent::responsibilityLevels() as $level)
                <div class="bg-white p-7">
                    <span class="text-sm font-semibold text-brass-600">0{{ $loop->iteration }}</span>
                    <h3 class="mt-3 text-lg font-semibold text-navy-900">{{ $level['title'] }}</h3>
                    <p class="mt-3 text-[0.95rem] leading-7 text-anthracite-700">{{ $level['text'] }}</p>
                </div>
            @endforeach
        </div>

        <p class="mt-8 flex items-start gap-3 text-sm font-medium text-navy-900">
            @include('site.partials.icon', ['name' => 'document', 'class' => 'mt-0.5 h-5 w-5 shrink-0 text-brass-600'])
            {{ \App\Support\Site\SiteContent::CONTRACT_NOTE }}
        </p>
    </div>
</section>
