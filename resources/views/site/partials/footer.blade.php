@php($services = \App\Support\Site\SiteContent::services())

<footer class="border-t border-white/10 bg-navy-950 text-slate-300">
    <div class="site-container py-16">
        <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
            <div>
                <a href="{{ route('home') }}" aria-label="RuddatTech – zur Startseite">
                    @include('site.partials.logo')
                </a>
                <p class="mt-5 max-w-sm text-sm leading-7 text-slate-400">
                    Komplexe Software. Zuverlässig betreut. Wartung, Stabilisierung und langfristige technische
                    Betreuung individueller Geschäftsanwendungen.
                </p>
                <a href="mailto:{{ \App\Support\Site\SiteContent::CONTACT_EMAIL }}"
                    class="mt-5 inline-flex items-center gap-2 text-sm font-medium text-slate-200 hover:text-brass-300">
                    @include('site.partials.icon', ['name' => 'mail', 'class' => 'h-4 w-4'])
                    {{ \App\Support\Site\SiteContent::CONTACT_EMAIL }}
                </a>
            </div>

            <div>
                <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Leistungen</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($services as $slug => $service)
                        <li><a class="transition hover:text-white" href="{{ route('leistungen.show', $slug) }}">{{ $service['title'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">RuddatTech</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a class="transition hover:text-white" href="{{ route('referenzen.index') }}">Referenzen</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('about') }}">Über RuddatTech</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('kontakt') }}">Kontakt</a></li>
                </ul>
            </div>

            <div>
                <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Rechtliches</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a class="transition hover:text-white" href="{{ route('impressum') }}">Impressum</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('datenschutz') }}">Datenschutz</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('agb') }}">AGB</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-3 border-t border-white/10 pt-8 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} RuddatTech · Ingo Ruddat</p>
            <p>{{ \App\Support\Site\SiteContent::CONTRACT_NOTE }}</p>
        </div>
    </div>
</footer>
