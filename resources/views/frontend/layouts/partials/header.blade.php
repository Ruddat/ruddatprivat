<header class="fixed inset-x-0 top-0 z-50 border-b border-slate-200/70 bg-white/90 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 py-4 lg:px-8">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-950 text-sm font-black text-white shadow-lg shadow-slate-950/20">IR</span>
            <span class="leading-tight">
                <span class="block text-base font-black tracking-tight text-slate-950">Ingo Ruddat</span>
                <span class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Laravel Systeme</span>
            </span>
        </a>

        <nav class="hidden items-center gap-7 text-sm font-bold text-slate-700 lg:flex">
            <a href="{{ url('/#about') }}" class="transition hover:text-pink-600">Profil</a>
            <a href="{{ url('/#services') }}" class="transition hover:text-pink-600">Leistungen</a>
            <a href="{{ url('/#work') }}" class="transition hover:text-pink-600">Projekte</a>
            <a href="{{ url('/#stack') }}" class="transition hover:text-pink-600">Stack</a>
            <a href="{{ url('/#contact') }}" class="transition hover:text-pink-600">Kontakt</a>
        </nav>

        <a class="inline-flex items-center justify-center rounded-full bg-pink-600 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition hover:-translate-y-0.5 hover:bg-pink-500" href="{{ url('/#contact') }}">Anfragen</a>
    </div>
</header>
