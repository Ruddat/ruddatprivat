<footer class="border-t border-slate-200 bg-slate-950 text-white">
    <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr_0.8fr_1fr]">
            <div>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-pink-600 text-sm font-black text-white">IR</span>
                    <span>
                        <span class="block text-lg font-black">Ingo Ruddat</span>
                        <span class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Laravel · Webprodukte · SEO</span>
                    </span>
                </a>
                <p class="mt-5 max-w-md leading-7 text-slate-300">
                    Ich entwickle digitale Systeme mit Laravel, Livewire, Adminbereichen, Automatisierung, KI-Workflows und skalierbaren Landingpage-Strukturen.
                </p>
            </div>

            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.24em] text-slate-400">Navigation</h3>
                <ul class="mt-5 space-y-3 text-sm font-semibold text-slate-300">
                    <li><a class="transition hover:text-white" href="{{ url('/#about') }}">Profil</a></li>
                    <li><a class="transition hover:text-white" href="{{ url('/#services') }}">Leistungen</a></li>
                    <li><a class="transition hover:text-white" href="{{ url('/#work') }}">Projekte</a></li>
                    <li><a class="transition hover:text-white" href="{{ url('/#contact') }}">Kontakt</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.24em] text-slate-400">Leistungen</h3>
                <ul class="mt-5 space-y-3 text-sm font-semibold text-slate-300">
                    <li>Laravel-Anwendungen</li>
                    <li>Livewire-Oberflächen</li>
                    <li>SEO-Landingpages</li>
                    <li>Automatisierung & KI</li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.24em] text-slate-400">Rechtliches</h3>
                <ul class="mt-5 space-y-3 text-sm font-semibold text-slate-300">
                    <li><a class="transition hover:text-white" href="{{ route('impressum') }}">Impressum</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('datenschutz') }}">Datenschutz</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('agb') }}">AGB</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-4 border-t border-white/10 pt-7 text-sm text-slate-400 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} Ingo Ruddat. Alle Rechte vorbehalten.</p>
            <p>Gebaut mit Laravel, Livewire und Tailwind CSS.</p>
        </div>
    </div>
</footer>
