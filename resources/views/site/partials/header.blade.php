@php($navigation = \App\Support\Site\SiteContent::navigation())

<header data-site-header class="fixed inset-x-0 top-0 z-50 border-b border-transparent bg-navy-950/80 backdrop-blur-md">
    <div class="site-container flex h-16 items-center justify-between gap-6 lg:h-20">
        <a href="{{ route('home') }}" class="group flex items-center gap-3" aria-label="RuddatTech – zur Startseite">
            @include('site.partials.logo')
        </a>

        <nav aria-label="Hauptnavigation" class="hidden items-center gap-1 lg:flex">
            @foreach ($navigation as $item)
                <a href="{{ route($item['route']) }}" class="nav-link"
                    @if (request()->routeIs($item['active'])) aria-current="page" @endif>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('kontakt') }}"
                class="hidden whitespace-nowrap rounded-md bg-brass-400 px-4 py-2.5 text-sm font-semibold text-navy-950 transition hover:bg-brass-300 sm:inline-flex">
                Betreuung anfragen
            </a>

            <button type="button" data-nav-toggle aria-expanded="false" aria-controls="mobile-nav"
                class="inline-flex h-11 w-11 items-center justify-center rounded-md text-slate-200 transition hover:bg-white/10 lg:hidden">
                <span class="sr-only">Menü öffnen</span>
                @include('site.partials.icon', ['name' => 'menu', 'class' => 'h-6 w-6'])
            </button>
        </div>
    </div>
</header>

{{-- Mobile Navigation --}}
<div id="mobile-nav" hidden class="fixed inset-0 z-[55] lg:hidden" role="dialog" aria-modal="true" aria-label="Navigation">
    <div class="absolute inset-0 bg-navy-950/70 backdrop-blur-sm" data-nav-close></div>

    <div class="mobile-nav-sheet relative mx-3 mt-3 overflow-hidden rounded-lg border border-white/10 bg-navy-900 shadow-2xl">
        <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
            @include('site.partials.logo')
            <button type="button" data-nav-close
                class="inline-flex h-11 w-11 items-center justify-center rounded-md text-slate-200 transition hover:bg-white/10">
                <span class="sr-only">Menü schließen</span>
                @include('site.partials.icon', ['name' => 'close', 'class' => 'h-6 w-6'])
            </button>
        </div>

        <nav aria-label="Mobile Hauptnavigation" class="px-2 py-3">
            <ul class="space-y-1">
                @foreach ($navigation as $item)
                    <li>
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center justify-between rounded-md px-3 py-3.5 text-base font-medium text-slate-100 transition hover:bg-white/5 aria-[current=page]:text-brass-300"
                            @if (request()->routeIs($item['active'])) aria-current="page" @endif>
                            {{ $item['label'] }}
                            @include('site.partials.icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4 text-slate-500'])
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="border-t border-white/10 p-4">
            <a href="{{ route('kontakt') }}" class="btn-primary w-full">Technische Betreuung anfragen</a>
            <a href="mailto:{{ \App\Support\Site\SiteContent::CONTACT_EMAIL }}"
                class="mt-3 flex items-center justify-center gap-2 py-2 text-sm text-slate-300 hover:text-white">
                @include('site.partials.icon', ['name' => 'mail', 'class' => 'h-4 w-4'])
                {{ \App\Support\Site\SiteContent::CONTACT_EMAIL }}
            </a>
        </div>
    </div>
</div>
