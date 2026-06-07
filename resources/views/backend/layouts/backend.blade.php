<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Backend' }} – RuddatTech</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-100 text-gray-800 antialiased">
    @php
        $navClass = function (string $routeName): string {
            return request()->routeIs($routeName)
                ? 'bg-pink-100 text-pink-700 font-semibold'
                : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600';
        };

        $navItems = [
            'general' => [
                ['label' => 'Dashboard', 'route' => 'admin.bookkeeping.dashboard'],
                ['label' => 'Dateibox', 'route' => 'admin.drive'],
                ['label' => 'ProjectHub', 'route' => 'admin.projecthub.index'],
            ],
            'bookkeeping' => [
                ['label' => 'Buchungen', 'route' => 'admin.bookkeeping.entries'],
                ['label' => 'Gewinn & Verlust', 'route' => 'admin.bookkeeping.report_profit_loss'],
                ['label' => 'Umsatzsteuer', 'route' => 'admin.bookkeeping.report_vat'],
                ['label' => 'Buchungsjahre', 'route' => 'admin.bookkeeping.fiscal_years'],
                ['label' => 'Mandanten', 'route' => 'admin.bookkeeping.tenants'],
                ['label' => 'Konten', 'route' => 'admin.bookkeeping.accounts'],
                ['label' => 'Eröffnungsbilanz', 'route' => 'admin.bookkeeping.opening_balance'],
            ],
            'utility_costs' => [
                ['label' => 'Abrechnung', 'route' => 'admin.utility_costs.billing_calculation'],
                ['label' => 'Abrechnungen verwalten', 'route' => 'admin.utility_costs.billing_generation'],
                ['label' => 'Abrechnungsköpfe', 'route' => 'admin.utility_costs.billing_headers'],
                ['label' => 'Mietobjekte', 'route' => 'admin.utility_costs.billing_table'],
                ['label' => 'Heizkosten', 'route' => 'admin.utility_costs.heating_costs'],
                ['label' => 'Rückzahlungen & Zahlungen', 'route' => 'admin.utility_costs.refunds_or_payments'],
                ['label' => 'Mietobjekte', 'route' => 'admin.utility_costs.rental_objects'],
                ['label' => 'Zahlungen verwalten', 'route' => 'admin.utility_costs.tenant_payments'],
                ['label' => 'Mieter', 'route' => 'admin.utility_costs.tenants'],
                ['label' => 'Nebenkosten erfassen', 'route' => 'admin.utility_costs.utility_cost_recording'],
                ['label' => 'Nebenkostenpositionen', 'route' => 'admin.utility_costs.utility_costs'],
            ],
            'portfolio' => [
                ['label' => 'Manager', 'route' => 'admin.portfolio.manager'],
                ['label' => 'Editor', 'route' => 'admin.portfolio.editor'],
            ],
        ];
    @endphp

    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-white shadow-lg flex-shrink-0 flex flex-col">
            <div class="px-6 py-4 border-b">
                <h1 class="text-lg font-bold text-pink-600">RuddatTech</h1>
            </div>

            <nav class="flex-1 overflow-y-auto mt-2 px-2 space-y-6">
                <div>
                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Allgemein</h3>

                    @foreach ($navItems['general'] as $item)
                        @if (Route::has($item['route']))
                            <a href="{{ route($item['route']) }}"
                                class="block px-3 py-2 rounded-lg text-sm font-medium {{ $navClass($item['route']) }}">
                                {{ $item['label'] }}
                            </a>
                        @endif
                    @endforeach

                    <a href="/" target="_blank" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600">
                        Zur Webseite
                    </a>
                </div>

                @if (Route::has('admin.bookkeeping.dashboard'))
                    <div class="px-3">
                        <a href="{{ route('admin.bookkeeping.dashboard') }}"
                            class="block w-full text-center bg-pink-600 text-white font-semibold py-2 px-3 rounded-lg shadow hover:bg-pink-700 transition">
                            + Buchen
                        </a>
                    </div>
                @endif

                <div>
                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Buchhaltung</h3>
                    <div class="space-y-1">
                        @foreach ($navItems['bookkeeping'] as $item)
                            @if (Route::has($item['route']))
                                <a href="{{ route($item['route']) }}"
                                    class="block px-3 py-2 rounded-lg text-sm font-medium {{ $navClass($item['route']) }}">
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                @php
                    $visibleUtilityItems = collect($navItems['utility_costs'])->filter(fn ($item) => Route::has($item['route']));
                @endphp

                @if ($visibleUtilityItems->isNotEmpty())
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Nebenkosten</h3>
                        <div class="space-y-1">
                            @foreach ($visibleUtilityItems as $item)
                                <a href="{{ route($item['route']) }}"
                                    class="block px-3 py-2 rounded-lg text-sm font-medium {{ $navClass($item['route']) }}">
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Portfolio</h3>
                    <div class="space-y-1">
                        @foreach ($navItems['portfolio'] as $item)
                            @if (Route::has($item['route']))
                                <a href="{{ route($item['route']) }}"
                                    class="block px-3 py-2 rounded-lg text-sm font-medium {{ $navClass($item['route']) }}">
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="px-3 mt-auto">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600">
                            Abmelden
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">Hallo, Admin</span>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=f472b6&color=fff" alt="Avatar" class="w-8 h-8 rounded-full shadow">
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                @if (request()->header('X-Livewire') || isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>

            <footer class="bg-white border-t">
                <div class="px-6 py-4 text-center text-xs text-gray-500">
                    © {{ date('Y') }} RuddatTech – Alle Rechte vorbehalten.
                </div>
            </footer>
        </div>
    </div>

    @livewireScripts
    <script src="{{ asset('js/projecthub-sortable.js') }}" defer></script>
</body>

</html>
