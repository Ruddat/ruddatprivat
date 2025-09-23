<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? "Backend" }} – RuddatTech</title>

    @vite(["resources/css/app.css", "resources/js/app.js"])
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-100 text-gray-800 antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg flex-shrink-0 flex flex-col">
            <!-- Logo -->
            <div class="px-6 py-4 border-b">
                <h1 class="text-lg font-bold text-pink-600">RuddatTech</h1>
            </div>

            <nav class="flex-1 overflow-y-auto mt-2 px-2 space-y-6">

                <!-- Hauptnavigation -->
                <div>
                    <h3
                        class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                        Allgemein
                    </h3>
                    <a href="{{ route("dashboard") }}"
                        class="block px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs("dashboard") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                        Dashboard
                    </a>

                    <a href="/" target="_blank"
                        class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600">
                        Zur Webseite
                    </a>
                </div>

                <!-- Buchen Primary Button -->
                <div class="px-3">
                    <a href="{{ route("admin.bookkeeping.dashboard") }}"
                        class="block w-full text-center bg-pink-600 text-white font-semibold py-2 px-3 rounded-lg shadow hover:bg-pink-700 transition">
                        + Buchen
                    </a>
                </div>

                <!-- Buchhaltung -->
                <div>
                    <h3
                        class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                        Buchhaltung
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route("admin.bookkeeping.entries") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.bookkeeping.entries") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Buchungen
                        </a>
                        <a href="{{ route("admin.bookkeeping.report_profit_loss") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.bookkeeping.report_profit_loss") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Gewinn & Verlust
                        </a>
                        <a href="{{ route("admin.bookkeeping.report_vat") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.bookkeeping.report_vat") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Umsatzsteuer
                        </a>
                        <a href="{{ route("admin.bookkeeping.fiscal_years") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.bookkeeping.fiscal_years") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Buchungsjahre
                        </a>
                        <a href="{{ route("admin.bookkeeping.tenants") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.bookkeeping.tenants") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Mandanten
                        </a>
                        <a href="{{ route("admin.bookkeeping.accounts") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.bookkeeping.accounts") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Konten
                        </a>
                        <a href="{{ route("admin.bookkeeping.opening_balance") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.bookkeeping.opening_balance") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Eröffnungsbilanz
                        </a>

                    </div>
                </div>

                {{-- Nebenkosten --}}
                <div>
                    <h3
                        class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                        Nebenkosten
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route("admin.utility_costs.billing_calculation") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.utility_costs.billing_calculation") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Abrechnung
                        </a>
                        <a href="{{ route("admin.utility_costs.billing_generation") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.utility_costs.billing_generation") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Abrechnungen verwalten
                        </a>

                        <a href="{{ route("admin.utility_costs.billing_headers") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.utility_costs.billing_headers") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Abrechnungsköpfe
                        </a>

                        <a href="{{ route("admin.utility_costs.billing_table") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.utility_costs.billing_table") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Mietobjekte
                        </a>

                        <a href="{{ route("admin.utility_costs.heating_costs") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.utility_costs.heating_costs") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Heizkosten
                        </a>
                        <a href="{{ route("admin.utility_costs.refunds_or_payments") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.utility_costs.refunds_or_payments") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Rückzahlungen & Zahlungen
                        </a>

                        <a href="{{ route("admin.utility_costs.rental_objects") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.utility_costs.rental_objects") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Mietobjekte
                        </a>

                        <a href="{{ route("admin.utility_costs.tenant_payments") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.utility_costs.tenant_payments") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Zahlungen verwalten
                        </a>

                        <a href="{{ route("admin.utility_costs.tenants") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.utility_costs.tenants") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Mieter
                        </a>

                        <a href="{{ route("admin.utility_costs.utility_cost_recording") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.utility_costs.utility_cost_recording") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Nebenkosten erfassen
                        </a>

                        <a href="{{ route("admin.utility_costs.utility_costs") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.utility_costs.utility_costs") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Nebenkostenpositionen
                        </a>

                    </div>
                </div>

                <!-- Portfolio -->
                <div>
                    <h3
                        class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                        Portfolio
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route("admin.portfolio.manager") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.portfolio.manager") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Manager
                        </a>
                        <a href="{{ route("admin.portfolio.editor") }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs("admin.portfolio.editor") ? "bg-pink-100 text-pink-700 font-semibold" : "text-gray-700 hover:bg-pink-50 hover:text-pink-600" }}">
                            Editor
                        </a>
                    </div>
                </div>

                <!-- Logout -->
                <div class="px-3 mt-auto">
                    <form method="POST" action="#">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600">
                            Abmelden
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">{{ $title ?? "Dashboard" }}</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">Hallo, Admin</span>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=f472b6&color=fff"
                        alt="Avatar" class="w-8 h-8 rounded-full shadow">
                </div>
            </header>

            <!-- Main -->
            <main class="flex-1 overflow-y-auto p-6">
                @if (request()->header("X-Livewire") || isset($slot))
                    {{ $slot }}
                @else
                    @yield("content")
                @endif
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t">
                <div class="px-6 py-4 text-center text-xs text-gray-500">
                    © {{ date("Y") }} RuddatTech – Alle Rechte vorbehalten.
                </div>
            </footer>
        </div>
    </div>

    @livewireScripts
</body>

</html>
