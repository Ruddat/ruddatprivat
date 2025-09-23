@extends("backend.customer.layouts.app")

@section("title", "Buchhaltung Dashboard")
@section("page_title", "Buchhaltung")

@section("content")

    {{-- Hero --}}
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-2xl font-bold">Hallo {{ $customer->name }},</h2>
        <p class="mt-2">Deine Buchhaltung in Zahlen und Fakten.</p>
        <p class="mt-1 text-sm text-blue-100">
            Belege, Salden und Finanzberichte immer griffbereit.
        </p>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-blue-600">Letzte Buchungen</h3>
            <p class="mt-2 text-gray-600">Neueste Einträge aus deiner Buchhaltung.</p>
            <a href="#" class="inline-block mt-4 text-sm text-blue-600 hover:underline">Ansehen</a>
        </div>

        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-blue-600">Saldenübersicht</h3>
            <p class="mt-2 text-gray-600">Offene und ausgeglichene Konten.</p>
            <a href="#" class="inline-block mt-4 text-sm text-blue-600 hover:underline">Details</a>
        </div>
    </div>

@endsection
