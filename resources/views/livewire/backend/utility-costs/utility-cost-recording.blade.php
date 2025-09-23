<div class="p-6">
    <!-- Kopfbereich -->
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Nebenkosten erfassen</h2>
        <p class="mt-2 text-sm text-gray-600">
            Hier können Sie neue Nebenkostenpositionen für ein Mietobjekt erfassen, anpassen oder
            löschen.
            Diese Positionen fließen später in die Jahresabrechnung mit ein.
        </p>
    </div>

    <!-- Formular -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">

    @if ($rentalObjects->isEmpty())
        <div class="text-center py-10">
            <p class="text-gray-700 mb-4">
                Es ist noch kein Mietobjekt vorhanden. Bitte legen Sie zuerst ein Mietobjekt an.
            </p>
            <a href="{{ route('customer.utility_costs.rental_objects') }}"
               class="inline-flex items-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md shadow hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                Mietobjekt anlegen
            </a>
        </div>
    @else
        <h5 class="text-lg font-medium text-gray-800 mb-4">
            {{ $editMode ? "Position bearbeiten" : "Neue Position hinzufügen" }}
        </h5>

        <form wire:submit.prevent="{{ $editMode ? "updateRecordedCost" : "addRecordedCost" }}"
            class="space-y-6">
            <!-- Mietobjekt -->
            <div>
                <label for="rental_object_id"
                    class="block text-sm font-medium text-gray-700">Mietobjekt:</label>
                <select wire:model="rental_object_id" id="rental_object_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    <option value="">Wählen...</option>
                    @foreach ($rentalObjects as $object)
                        <option value="{{ $object->id }}">{{ $object->street }},
                            {{ $object->house_number }}, {{ $object->city }}</option>
                    @endforeach
                </select>
                @error("rental_object_id")
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jahr -->
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700">Jahr:</label>
                <select wire:model.change="year" id="year" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    @for ($i = date("Y"); $i >= date("Y") - 20; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                @error("year")
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nebenkostenposition -->
            <div>
                <label for="utility_cost_id"
                    class="block text-sm font-medium text-gray-700">Nebenkostenposition:</label>
                <select wire:model="utility_cost_id" id="utility_cost_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    <option value="">Wählen...</option>
                    @foreach ($utilityCosts as $cost)
                        <option value="{{ $cost->id }}">{{ $cost->name }}</option>
                    @endforeach
                </select>
                @error("utility_cost_id")
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Benutzerdefinierter Name -->
            <div>
                <label for="custom_name"
                    class="block text-sm font-medium text-gray-700">Benutzerdefinierter Name
                    (optional):</label>
                <input type="text" wire:model="custom_name" id="custom_name"
                    placeholder="Zusätzliche Position"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error("custom_name")
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Betrag -->
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">Betrag
                    (€):</label>
                <input type="number" step="0.01" wire:model="amount" id="amount" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error("amount")
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Verteilerschlüssel -->
            <div>
                <label for="distribution_key"
                    class="block text-sm font-medium text-gray-700">Verteilerschlüssel:</label>
                <select wire:model="distribution_key" id="distribution_key"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    <option value="units">Nach Einheiten</option>
                    <option value="people">Nach Personen</option>
                    <option value="area">Wohnfläche</option>
                    <option value="consumption">Nach Verbrauch</option>
                </select>
                @error("distribution_key")
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md shadow hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    {{ $editMode ? "Position aktualisieren" : "Position hinzufügen" }}
                </button>
                @if ($editMode)
                    <button type="button" wire:click="resetFields"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md shadow hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                        Abbrechen
                    </button>
                @endif
            </div>
        </form>
        @endif

    </div>

    <!-- Tabelle -->
    <div class="bg-white shadow rounded-lg p-6">
        <h5 class="text-lg font-medium text-gray-800 mb-4">Erfasste Nebenkosten für
            {{ $year }}</h5>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Name der Position
                        </th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Beschreibung</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Verteilerschlüssel
                        </th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Betrag (€)</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($recordedCosts as $cost)
                        <tr>
                            <td class="px-4 py-2">
                                {{ $cost->utilityCost ? $cost->utilityCost->name : $cost->custom_name }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $cost->utilityCost ? $cost->utilityCost->description : "-" }}
                            </td>
                            <td class="px-4 py-2">{{ $cost->distribution_key }}</td>
                            <td class="px-4 py-2">{{ number_format($cost->amount, 2, ",", ".") }}
                            </td>
                            <td class="px-4 py-2 flex gap-2">
                                <button wire:click="editRecordedCost({{ $cost->id }})"
                                    class="px-2 py-1 bg-yellow-500 text-white text-xs rounded-md hover:bg-yellow-600">
                                    Bearbeiten
                                </button>
                                <button wire:click="deleteRecordedCost({{ $cost->id }})"
                                    onclick="return confirm('Möchten Sie diese Position wirklich löschen?')"
                                    class="px-2 py-1 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">
                                    Löschen
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 font-semibold">
                        <td colspan="4" class="px-4 py-2 text-right">Gesamtkosten:</td>
                        <td class="px-4 py-2">{{ number_format($totalCosts, 2, ",", ".") }} €</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
