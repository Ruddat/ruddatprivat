<div class="p-6">
    <!-- Title Section -->
    <div class="bg-white shadow rounded-lg p-6">
        <h5 class="text-lg font-semibold text-gray-800 mb-4">
            Abrechnung für das Jahr und Mietobjekt auswählen
        </h5>

        <!-- Alerts -->
        @if (session("error"))
            <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                {{ session("error") }}
            </div>
        @endif

        @if (session("success"))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                {{ session("success") }}
            </div>
        @endif

        <!-- Objektauswahl -->
        <div class="mb-4">
            <label for="rental_object_id" class="block text-sm font-medium text-gray-700">
                Mietobjekt:
            </label>
            <select wire:model="rental_object_id" id="rental_object_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
                required>
                <option value="">Bitte auswählen...</option>
                @foreach ($rentalObjects as $object)
                    <option value="{{ $object->id }}">
                        {{ $object->name }}, {{ $object->street }}, {{ $object->city }}
                    </option>
                @endforeach
            </select>
            @error("rental_object_id")
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Jahr-Auswahl -->
        <div class="mb-4">
            <label for="year" class="block text-sm font-medium text-gray-700">
                Jahr auswählen:
            </label>
            <select wire:model="year" id="year"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
                required>
                @for ($i = date("Y"); $i >= date("Y") - 20; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
            @error("year")
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button wire:click="calculateAndSaveAnnualBilling"
            class="inline-flex items-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md shadow hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
            Abrechnung berechnen
        </button>
    </div>

    <!-- Abrechnungsergebnisse -->
    <div class="bg-white shadow rounded-lg p-6 mt-8 overflow-x-auto">
        <h5 class="text-lg font-semibold text-gray-800 mb-4">
            Abrechnungsergebnisse für das Jahr: {{ $year }}
        </h5>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Mieter</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Mietdauer</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Abrechnungstyp</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Details</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Gesamtkosten (€)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($calculatedCosts as $entry)
                    <tr>
                        <td class="px-4 py-2">
                            {{ $entry["tenant"]->first_name }} {{ $entry["tenant"]->last_name }}
                        </td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($entry["tenant"]->start_date)->format("d.m.Y") }}
                            -
                            {{ $entry["tenant"]->end_date ? \Carbon\Carbon::parse($entry["tenant"]->end_date)->format("d.m.Y") : "Heute" }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $entry["tenant"]->billing_type === "units" ? "Einheiten" : "Personen" }}
                        </td>
                        <td class="px-4 py-2">
                            @if (!empty($entry["utility_details"]))
                                @foreach ($entry["utility_details"] as $utility)
                                    <div>
                                        <span
                                            class="font-semibold">{{ $utility["short_name"] }}:</span>
                                        {{ number_format($utility["amount"], 2, ",", ".") }} €
                                    </div>
                                @endforeach
                            @else
                                <div class="text-gray-400">Keine Details verfügbar</div>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            {{ number_format($entry["total_cost"], 2, ",", ".") }} €
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Heiz- und Warmwasserkosten -->
    <div class="bg-white shadow rounded-lg p-6 mt-8 overflow-x-auto">
        <h5 class="text-lg font-semibold text-gray-800 mb-4">
            Zusätzliche Kosten: Heiz- und Warmwasserkosten
        </h5>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Mieter</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Mietdauer</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Heizkosten (€)</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Warmwasserkosten (€)
                    </th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Gesamtkosten (€)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($calculatedCosts as $entry)
                    <tr>
                        <td class="px-4 py-2">
                            {{ $entry["tenant"]->first_name }} {{ $entry["tenant"]->last_name }}
                        </td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($entry["tenant"]->start_date)->format("d.m.Y") }}
                            -
                            {{ $entry["tenant"]->end_date ? \Carbon\Carbon::parse($entry["tenant"]->end_date)->format("d.m.Y") : "Heute" }}
                        </td>
                        <td class="px-4 py-2">
                            {{ number_format($entry["heating_cost"] - $entry["warm_water_cost"], 2, ",", ".") }}
                            €
                        </td>
                        <td class="px-4 py-2">
                            {{ number_format($entry["warm_water_cost"], 2, ",", ".") }} €
                        </td>
                        <td class="px-4 py-2">
                            {{ number_format($entry["heating_cost"], 2, ",", ".") }} €
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
