{{-- resources/views/backend/admin/dashboard.blade.php --}}

@extends('backend.admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Übersicht')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-orange-600">Kundenverwaltung</h3>
            <p class="mt-2 text-gray-600">Kunden verwalten und Kundenansichten öffnen.</p>
            <a href="{{ route('admin.customers.index') }}"
                class="inline-block mt-4 text-sm text-orange-600 hover:underline">Verwalten</a>
        </div>

        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-orange-600">Cloud</h3>
            <p class="mt-2 text-gray-600">Dateien, Ordner und Freigaben verwalten.</p>
            <a href="{{ route('admin.drive') }}"
                class="inline-block mt-4 text-sm text-orange-600 hover:underline">Öffnen</a>
        </div>

        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-orange-600">ProjectHub</h3>
            <p class="mt-2 text-gray-600">Boards und interne Projekte verwalten.</p>
            <a href="{{ route('admin.projecthub.index') }}"
                class="inline-block mt-4 text-sm text-orange-600 hover:underline">Öffnen</a>
        </div>

        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-orange-600">Kundenfeedback</h3>
            <p class="mt-2 text-gray-600">Rückmeldungen der Kunden prüfen und bearbeiten.</p>
            <a href="{{ route('admin.customers.feedback') }}"
                class="inline-block mt-4 text-sm text-orange-600 hover:underline">Anzeigen</a>
        </div>

        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-orange-600">Systemeinstellungen</h3>
            <p class="mt-2 text-gray-600">Zentrale Einstellungen der Anwendung verwalten.</p>
            <a href="{{ route('admin.settings') }}"
                class="inline-block mt-4 text-sm text-orange-600 hover:underline">Bearbeiten</a>
        </div>
    </div>
@endsection
