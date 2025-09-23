@extends("backend.customer.layouts.app")

@section("title", "Nebenkosten Dashboard")
@section("page_title", "Nebenkosten")

@section("content")

    {{-- Hero --}}
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-2xl font-bold">Hallo {{ $customer->name }},</h2>
        <p class="mt-2">Alles rund um deine Nebenkostenabrechnung.</p>
        <p class="mt-1 text-sm text-green-100">
            Mietobjekte, Heizkosten und Abrechnungen im Überblick.
        </p>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-green-600">Meine Mietobjekte</h3>
            <p class="mt-2 text-gray-600">Verwaltung deiner Objekte und Mieter.</p>
            <a href="#" class="inline-block mt-4 text-sm text-green-600 hover:underline">Ansehen</a>
        </div>

        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-green-600">Letzte Abrechnungen</h3>
            <p class="mt-2 text-gray-600">Einblick in deine letzten Nebenkostenabrechnungen.</p>
            <a href="#" class="inline-block mt-4 text-sm text-green-600 hover:underline">Details</a>
        </div>
    </div>

@endsection
