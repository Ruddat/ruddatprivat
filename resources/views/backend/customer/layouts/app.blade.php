{{-- resources/views/backend/customer/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="de" x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('darkMode') === 'true', userMenuOpen: false }" x-init="$watch('darkMode', v => localStorage.setItem('darkMode', v))"
    x-bind:class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield("title", "Customer Dashboard")</title>
    @vite(["resources/css/app.css", "resources/js/app.js"])
    @livewireStyles
</head>

<body class="bg-gray-100 text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100">

    <div class="flex min-h-screen">

        {{-- Overlay für Mobile Sidebar --}}
        <div class="fixed inset-0 bg-black/40 z-40 md:hidden" x-show="sidebarOpen"
            x-transition.opacity @click="sidebarOpen = false"></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 shadow-lg transform transition-transform duration-200 z-50
               md:translate-x-0 md:static md:inset-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <div
                class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center md:block">
                <h1 class="text-xl font-bold text-pink-600 dark:text-pink-400">Customer</h1>
                <button class="md:hidden text-gray-500 hover:text-gray-700 dark:text-gray-300"
                    @click="sidebarOpen = false">✕</button>
            </div>

            <nav class="p-4 space-y-2">
{{-- Dashboard --}}
<a href="{{ route('customer.dashboard') }}"
   class="block px-4 py-2 rounded transition 
          {{ request()->routeIs('customer.dashboard') ? 'bg-pink-100 text-pink-700 font-semibold' : 'hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700' }}">
   Dashboard
</a>

{{-- Modul Dashboards --}}
<a href="{{ route('customer.dashboard.rechnungen') }}"
   class="block px-4 py-2 rounded transition 
          {{ request()->routeIs('customer.dashboard.rechnungen') ? 'bg-pink-100 text-pink-700 font-semibold' : 'hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700' }}">
   Rechnungen
</a>

<a href="{{ route('customer.dashboard.buchhaltung') }}"
   class="block px-4 py-2 rounded transition 
          {{ request()->routeIs('customer.dashboard.buchhaltung') ? 'bg-pink-100 text-pink-700 font-semibold' : 'hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700' }}">
   Buchhaltung
</a>

<a href="{{ route('customer.dashboard.nebenkosten') }}"
   class="block px-4 py-2 rounded transition 
          {{ request()->routeIs('customer.dashboard.nebenkosten') ? 'bg-pink-100 text-pink-700 font-semibold' : 'hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700' }}">
   Nebenkosten
</a>




                {{-- Buchhaltung (Collapsible) --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open"
                        class="flex justify-between w-full px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                        <span>Buchhaltung</span>
                        <svg :class="{ 'rotate-180': open }"
                            class="w-4 h-4 transform transition-transform" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="pl-6 space-y-1">
                        <a href="#"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Übersicht
                        </a>
                        <a href="#"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Buchungen
                        </a>
                        <a href="#"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Reports
                        </a>
                    </div>
                </div>

{{-- Rechnungen (Collapsible) --}}
<div 
    x-data="{ open: {{ request()->routeIs('customer.e_invoice.*') || request()->routeIs('customer.new_invoice.*') ? 'true' : 'false' }} }" 
    class="space-y-1"
>
    <button @click="open = !open"
        class="flex justify-between w-full px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
        <span>Meine Rechnungen</span>
        <svg :class="{ 'rotate-180': open }"
            class="w-4 h-4 transform transition-transform" fill="none"
            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" x-collapse class="pl-6 space-y-1">
        <a href="{{ route('customer.e_invoice.invoice_headers') }}"
            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700
            {{ request()->routeIs('customer.e_invoice.invoice_headers') ? 'bg-pink-100 text-pink-700 font-semibold' : '' }}">
            Rechnungsköpfe
        </a>
        <a href="{{ route('customer.new_invoice.invoice_manager') }}"
            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700
            {{ request()->routeIs('customer.new_invoice.invoice_manager') ? 'bg-pink-100 text-pink-700 font-semibold' : '' }}">
            E-Invoices
        </a>
        <a href="{{ route('customer.new_invoice.pdf_manager') }}"
            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700
            {{ request()->routeIs('customer.new_invoice.pdf_manager') ? 'bg-pink-100 text-pink-700 font-semibold' : '' }}">
            Rechnungen PDF
        </a>
    </div>
</div>


                {{-- Nebenkosten (Collapsible) --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open"
                        class="flex justify-between w-full px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                        <span>Nebenkosten</span>
                        <svg :class="{ 'rotate-180': open }"
                            class="w-4 h-4 transform transition-transform" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="pl-6 space-y-1">

                        <a href="{{ route("customer.utility_costs.billing_table") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Abrechnungstabelle
                        </a>



                        <a href="{{ route("customer.utility_costs.billing_headers") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Abrechnungsköpfe
                        </a>

                        <a href="{{ route("customer.utility_costs.rental_objects") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Mietobjekte
                        </a>

                        <a href="{{ route("customer.utility_costs.tenants") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Mieter
                        </a>

                        <a href="{{ route("customer.utility_costs.tenant_payments") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Nebenkostenzahlungen
                        </a>

                       
                        <a href="{{ route("customer.utility_costs.refunds_or_payments") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Rückzahlungen / Nachzahlungen
                        </a>

                        <a href="{{ route("customer.utility_costs.utility_cost_recording") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Nebenkosten erfassen
                        </a>

                        <a href="{{ route("customer.utility_costs.heating_costs") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Heizkosten erfassen
                        </a>

                        <a href="{{ route("customer.utility_costs.billing_calculation") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Abrechnung erstellen fuer das Jahr
                        </a>

                        <a href="{{ route("customer.utility_costs.billing_table") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Abrechnungstabelle
                        </a>







                        <a href="{{ route("customer.utility_costs.billing_generation") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Abrechnung herunterladen
                        </a>





                        <a href="#"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Kostenarten
                        </a>



                        <a href="{{ route("customer.utility_costs.utility_costs") }}"
                            class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                            Nebenkosten Übersicht
                        </a>

                    </div>
                </div>

                {{-- Profil --}}
                <a href="{{ route('customer.profile') }}"
                    class="block px-4 py-2 rounded hover:bg-pink-50 hover:text-pink-600 dark:hover:bg-gray-700">
                    Profil
                </a>
            </nav>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col">

            {{-- Header --}}
            <header class="bg-white dark:bg-gray-800 shadow p-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold">@yield("page_title", "Dashboard")</h2>

                <div class="flex items-center space-x-4">

                    {{-- Darkmode Toggle --}}
                    <button @click="darkMode = !darkMode"
                        class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600">
                        <span x-show="!darkMode">🌙</span>
                        <span x-show="darkMode">☀️</span>
                    </button>

                    {{-- User Menu --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 focus:outline-none">
                            <span class="hidden sm:inline text-sm font-medium">
                                {{ auth("customer")->user()->name ?? "Gast" }}
                            </span>
                            <svg class="w-4 h-4 text-gray-500" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded shadow-lg z-50">
                            <div
                                class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 border-b dark:border-gray-600">
                                {{ auth("customer")->user()->email ?? "" }}
                            </div>
                            <a href="{{ route('customer.profile') }}"
                                class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">Profil</a>
                            <form method="POST" action="{{ route("customer.logout") }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Sidebar Toggle Mobile --}}
                    <button class="md:hidden text-gray-600 hover:text-gray-800 dark:text-gray-300"
                        @click="sidebarOpen = true">☰</button>
                </div>
            </header>

            {{-- Content --}}
            <main class="p-6 flex-1">

                <div class=''>

@if(session()->has('impersonator_id'))
    @php
        $admin = \App\Models\Admin::find(session('impersonator_id'));
        $customer = Auth::guard('customer')->user();
    @endphp

    <div class="bg-yellow-200 text-yellow-800 p-3 mb-4 rounded">
        ⚠️ Du bist aktuell als Customer <strong>{{ $customer->name }}</strong> eingeloggt
        @if($admin) – gestartet von <strong>{{ $admin->name }}</strong> @endif
        (<a href="{{ route('customer.impersonate.stop') }}" class="underline font-bold">
            Zurück zum Admin
        </a>)
    </div>
@endif

                </div>

                @yield("content")
            </main>

    {{-- Footer --}}
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 text-center text-sm text-gray-500">
        <span class="font-medium text-pink-600">Papierkram</span> &copy; {{ date('Y') }} – Alle Rechte vorbehalten.
    </footer>


        </div>
    </div>
    @livewireScripts
</body>

</html>
