{{-- resources/views/backend/admin/dashboard.blade.php --}}

@extends("backend.admin.layouts.app")

@section("title", "Admin Dashboard")
@section("page_title", "Übersicht")

@section("content")
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Card 1 --}}
        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-orange-600">Benutzerverwaltung</h3>
            <p class="mt-2 text-gray-600">Admins, Kunden und Anbieter verwalten.</p>
            <a href="#"
                class="inline-block mt-4 text-sm text-orange-600 hover:underline">Verwalten</a>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-orange-600">Buchhaltung</h3>
            <p class="mt-2 text-gray-600">Alle Buchungen, Konten und Reports einsehen.</p>
            <a href="{{ route("admin.bookkeeping.dashboard") }}"
                class="inline-block mt-4 text-sm text-orange-600 hover:underline">Öffnen</a>
        </div>

        {{-- Card 3 --}}
        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-orange-600">Portfolio</h3>
            <p class="mt-2 text-gray-600">Projekte und Referenzen verwalten.</p>
            <a href="{{ route("admin.portfolio.manager") }}"
                class="inline-block mt-4 text-sm text-orange-600 hover:underline">Bearbeiten</a>
        </div>

        {{-- Card 4 --}}
        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-orange-600">Nebenkosten</h3>
            <p class="mt-2 text-gray-600">Verbrauch, Kosten und Abrechnungen prüfen.</p>
            <a href=""
                class="inline-block mt-4 text-sm text-orange-600 hover:underline">Details</a>
        </div>

        {{-- Card 5 --}}
        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-orange-600">Systemberichte</h3>
            <p class="mt-2 text-gray-600">Logs, Statistiken und Systemstatus überwachen.</p>
            <a href="#"
                class="inline-block mt-4 text-sm text-orange-600 hover:underline">Anzeigen</a>
        </div>
    </div>

    {{-- Container für Livewire Tabelle --}}
    <div class="mt-10 bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Customer Verwaltung</h2>
        @livewire("backend.admin.customer.customers-table")
    </div>
@endsection
