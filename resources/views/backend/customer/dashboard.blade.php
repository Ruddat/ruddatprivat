{{-- resources/views/backend/customer/dashboard.blade.php --}}

@extends("backend.customer.layouts.app")

@section("title", "Customer Dashboard")
@section("page_title", "Übersicht")

@section("content")

    {{-- Hero Begrüßung --}}
    <div class="bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-2xl font-bold">
            Hallo {{ $customer->name }},
        </h2>
        <p class="mt-2 text-base">
            Willkommen bei <span class="font-semibold">Papierkram</span> 👋
        </p>
        <p class="mt-1 text-sm text-pink-100">
            Hier findest du praktische Tools für deine Verwaltung:
            <span class="font-medium">E-Rechnung</span>, 
            <span class="font-medium">Buchhaltung</span> 
            und <span class="font-medium">Nebenkostenabrechnung</span>.
        </p>
        <p class="mt-1 text-sm text-pink-100">
            Wir entwickeln ständig neue Funktionen, um dir die Arbeit noch leichter zu machen.
        </p>
    </div>

    {{-- Plan Info --}}
{{-- Plan Info --}}
<div class="mb-6 flex items-center justify-between bg-white dark:bg-gray-800 p-4 rounded shadow">
    <p class="text-sm text-gray-600">
        Dein aktueller Plan: 
        <span class="font-semibold text-pink-600">{{ ucfirst($customer->plan ?? 'free') }}</span>
    </p>

    @if($customer->plan === 'free')
        <a href="#"
           class="bg-pink-600 hover:bg-pink-700 text-white text-sm px-4 py-2 rounded shadow">
            Upgrade
        </a>
    @endif
</div>

{{-- Dashboard Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    {{-- Card 1 --}}
    <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
        <h3 class="text-lg font-semibold text-pink-600">Meine Rechnungen</h3>
        <p class="mt-2 text-gray-600">Alle E-Rechnungen einsehen und herunterladen.</p>
        <a href="#" class="inline-block mt-4 text-sm text-pink-600 hover:underline">Ansehen</a>
    </div>

    {{-- Card 2 --}}
    <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
        <h3 class="text-lg font-semibold text-pink-600">Nebenkosten</h3>
        <p class="mt-2 text-gray-600">Verbrauchsdaten und Abrechnungen prüfen.</p>
        <a href="#" class="inline-block mt-4 text-sm text-pink-600 hover:underline">Details</a>
    </div>

    {{-- Card 3 --}}
    <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
        <h3 class="text-lg font-semibold text-pink-600">Profil</h3>
        <p class="mt-2 text-gray-600">Persönliche Daten und Einstellungen verwalten.</p>
        <a href="#" class="inline-block mt-4 text-sm text-pink-600 hover:underline">Bearbeiten</a>
    </div>

    {{-- Card 4: Limits --}}
    <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
        <h3 class="text-lg font-semibold text-pink-600">Limits</h3>
        <ul class="mt-2 text-sm text-gray-700 space-y-2">
            @forelse($limits as $limit)
                <li class="flex justify-between items-center">
                    <span>{{ $limit['label'] }}</span>
                    <span class="font-semibold">
                        {{ $limit['used'] }} / {{ $limit['max'] ?: '∞' }}
                    </span>
                </li>
            @empty
                <li>Keine Limits definiert.</li>
            @endforelse
        @if($customer->plan === 'free')
    <div class="mt-4">
        <a href="#"
           class="bg-pink-600 hover:bg-pink-700 text-white text-xs px-3 py-1.5 rounded">
            Mehr Freigabe erhalten → Upgrade
        </a>
    </div>
@endif
        
        </ul>
    </div>
</div>


    {{-- Onboarding Modal --}}
@if(session('needs_onboarding'))
<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-[9999]">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6">
        <h5 class="text-lg font-semibold mb-4">Onboarding abschließen</h5>

        @if(session('verify_phone'))
            {{-- Code-Eingabe --}}
<form method="POST" action="{{ route('customer.onboarding.verify') }}">
    @csrf
    <label class="form-label">Bestätigungscode</label>
    <input type="text" class="form-control" name="code" required>
    <div class="mt-6 flex justify-end">
        <button type="submit"
                class="btn text-white fw-semibold"
                style="background: linear-gradient(90deg,#ec4899,#db2777); font-size: 0.95rem;">
            Verifizieren
        </button>
    </div>
</form>
        @else
            {{-- Basisdaten + Telefonnummer --}}
            <form method="POST" action="{{ route('customer.onboarding.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Straße</label>
                        <input type="text" class="form-control" name="street"
                               value="{{ old('street', $customer->street) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Nr.</label>
                        <input type="text" class="form-control" name="house_number"
                               value="{{ old('house_number', $customer->house_number) }}" required>
                    </div>
                    <div>
                        <label class="form-label">PLZ</label>
                        <input type="text" class="form-control" name="zip"
                               value="{{ old('zip', $customer->zip) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Ort</label>
                        <input type="text" class="form-control" name="city"
                               value="{{ old('city', $customer->city) }}" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Telefon</label>
                        <input type="text" class="form-control" name="phone"
                               value="{{ old('phone', $customer->phone) }}" required>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="btn text-white fw-semibold"
                            style="background: linear-gradient(90deg,#ec4899,#db2777); font-size: 0.95rem;">
                        Code anfordern
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endif


@endsection
