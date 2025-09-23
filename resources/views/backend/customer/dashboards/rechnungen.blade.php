@extends("backend.customer.layouts.app")

@section("title", "Rechnungen Dashboard")
@section("page_title", "Rechnungen")

@section("content")

    {{-- Hero --}}
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-2xl font-bold">Hallo {{ $customer->name }},</h2>
        <p class="mt-2">Hier hast du deine Rechnungen im Blick.</p>
        <p class="mt-1 text-sm text-purple-100">
            Offene, bezahlte und neue Rechnungen – alles an einem Ort.
        </p>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-purple-600">Letzte Rechnungen</h3>
            <p class="mt-2 text-gray-600">Übersicht der letzten Dokumente.</p>
            <a href="#" class="inline-block mt-4 text-sm text-purple-600 hover:underline">Ansehen</a>
        </div>

        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-purple-600">Offene Beträge</h3>
            <p class="mt-2 text-gray-600">Summe der noch offenen Rechnungen.</p>
            <a href="#" class="inline-block mt-4 text-sm text-purple-600 hover:underline">Details</a>
        </div>
    </div>

@endsection
