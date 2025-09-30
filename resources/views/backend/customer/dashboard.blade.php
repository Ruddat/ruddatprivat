@extends("backend.customer.layouts.app")

@section("title", "Customer Dashboard")
@section("page_title", "Übersicht")

@section("content")

    {{-- Hero Begrüßung --}}
    <div class="bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg shadow p-4 sm:p-6 mb-6">
        <h2 class="text-xl sm:text-2xl font-bold">
            Hallo {{ $customer->name }},
        </h2>
        <p class="mt-2 text-sm sm:text-base">
            Willkommen bei <span class="font-semibold">Papierkram</span> 👋
        </p>
        <p class="mt-1 text-xs sm:text-sm text-pink-100">
            Tools für <span class="font-medium">E-Rechnung</span>,
            <span class="font-medium">Buchhaltung</span> und 
            <span class="font-medium">Nebenkosten</span>.
        </p>
    </div>

    {{-- Plan Info --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-gray-800 p-4 rounded shadow">
        <p class="text-sm text-gray-600">
            Dein aktueller Plan:
            <span class="font-semibold text-pink-600">{{ ucfirst($customer->plan ?? 'free') }}</span>
        </p>
        @if($customer->plan === 'free')
            <a href="#"
               class="inline-block bg-pink-600 hover:bg-pink-700 text-white text-sm px-3 py-2 rounded shadow text-center">
                Upgrade
            </a>
        @endif
    </div>

    {{-- Dashboard Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div class="bg-white p-4 sm:p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-base sm:text-lg font-semibold text-pink-600">Meine Rechnungen</h3>
            <p class="mt-2 text-gray-600 text-sm">Alle E-Rechnungen einsehen und herunterladen.</p>
            <a href="#" class="inline-block mt-3 text-sm text-pink-600 hover:underline">Ansehen</a>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-base sm:text-lg font-semibold text-pink-600">Nebenkosten</h3>
            <p class="mt-2 text-gray-600 text-sm">Verbrauchsdaten und Abrechnungen prüfen.</p>
            <a href="#" class="inline-block mt-3 text-sm text-pink-600 hover:underline">Details</a>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-base sm:text-lg font-semibold text-pink-600">Profil</h3>
            <p class="mt-2 text-gray-600 text-sm">Persönliche Daten verwalten.</p>
            <a href="#" class="inline-block mt-3 text-sm text-pink-600 hover:underline">Bearbeiten</a>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-base sm:text-lg font-semibold text-pink-600">Limits</h3>
            <ul class="mt-2 text-xs sm:text-sm text-gray-700 space-y-1">
                @forelse($limits as $limit)
                    <li class="flex justify-between">
                        <span>{{ $limit['label'] }}</span>
                        <span class="font-semibold">{{ $limit['used'] }} / {{ $limit['max'] ?: '∞' }}</span>
                    </li>
                @empty
                    <li>Keine Limits definiert.</li>
                @endforelse
            </ul>
            @if($customer->plan === 'free')
                <div class="mt-3">
                    <a href="#"
                       class="inline-block bg-pink-600 hover:bg-pink-700 text-white text-xs px-3 py-1.5 rounded">
                        Mehr Freigabe → Upgrade
                    </a>
                </div>
            @endif
        </div>
    </div>




























    {{-- Onboarding Modal --}}
    @if(session('needs_onboarding'))
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-[9999] p-4">
            <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 space-y-4">
                <h5 class="text-lg font-semibold">Onboarding abschließen</h5>

                @if(session('verify_phone'))
                    <form method="POST" action="{{ route('customer.onboarding.verify') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bestätigungscode</label>
                            <input type="text" name="code"
                                   class="mt-1 block w-full rounded border-gray-300 focus:border-pink-500 focus:ring-pink-500 sm:text-sm" required>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded shadow">
                                Verifizieren
                            </button>
                        </div>
                    </form>
                @else
                    <form method="POST" action="{{ route('customer.onboarding.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Straße</label>
                            <input type="text" name="street" value="{{ old('street', $customer->street) }}"
                                   class="mt-1 block w-full rounded border-gray-300 focus:border-pink-500 focus:ring-pink-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nr.</label>
                            <input type="text" name="house_number" value="{{ old('house_number', $customer->house_number) }}"
                                   class="mt-1 block w-full rounded border-gray-300 focus:border-pink-500 focus:ring-pink-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">PLZ</label>
                            <input type="text" name="zip" value="{{ old('zip', $customer->zip) }}"
                                   class="mt-1 block w-full rounded border-gray-300 focus:border-pink-500 focus:ring-pink-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ort</label>
                            <input type="text" name="city" value="{{ old('city', $customer->city) }}"
                                   class="mt-1 block w-full rounded border-gray-300 focus:border-pink-500 focus:ring-pink-500 sm:text-sm" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Telefon</label>
                            <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                                   class="mt-1 block w-full rounded border-gray-300 focus:border-pink-500 focus:ring-pink-500 sm:text-sm" required>
                        </div>
                        <div class="sm:col-span-2 flex justify-end">
                            <button type="submit"
                                    class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded shadow">
                                Code anfordern
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

@endsection
