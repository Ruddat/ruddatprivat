<div class="p-6">
    <h2 class="text-xl font-semibold mb-6">Neue Abrechnung erstellen</h2>

    <!-- Erstellungsformular -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h5 class="text-lg font-medium text-gray-800">Abrechnungsdetails</h5>

        <form class="mt-4 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Auswahl des Abrechnungskopfs -->
                <div>
<x-form.select label="Abrechnungskopf auswählen" id="billing_header" wire:model="selectedHeaderId">
    <option value="">Wählen...</option>
    @foreach ($billingHeaders as $header)
        <option value="{{ $header->id }}">
            {{ $header->creator_name }} - {{ $header->city }}
        </option>
    @endforeach
</x-form.select>
                </div>

                <!-- Auswahl des Mietobjekts -->
                <div>
<x-form.select label="Mietobjekt auswählen" id="rental_object" wire:model="selectedRentalObjectId">
    <option value="">Wählen...</option>
    @foreach ($rentalObjects as $object)
        <option value="{{ $object->id }}">
            {{ $object->name }} - {{ $object->city }}
        </option>
    @endforeach
</x-form.select>
                </div>

                <!-- Auswahl des Mieters -->
                <div>
<x-form.select label="Mieter auswählen" id="tenant" wire:model="selectedTenantId">
    <option value="">Wählen...</option>
    @foreach ($tenants as $tenant)
        <option value="{{ $tenant->id }}">
            {{ $tenant->first_name }} {{ $tenant->last_name }} - {{ $tenant->city }}
        </option>
    @endforeach
</x-form.select>
                </div>

                <!-- Abrechnungszeitraum -->
                <div>
<x-form.input label="Abrechnungszeitraum" id="billing_period"
              wire:model="billingPeriod"
              placeholder="z.B. Januar 2023 - Dezember 2023"/>
                </div>
            </div>

<div class="flex justify-end">
    <button
        wire:click.prevent="generateBilling"
        wire:loading.attr="disabled"
        class="inline-flex items-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md shadow hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">

        <!-- Normaler Button Text -->
        <span wire:loading.remove wire:target="generateBilling">
            Abrechnung als PDF erstellen
        </span>

        <!-- Loading Spinner -->
        <span wire:loading.inline wire:target="generateBilling" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            Wird erstellt...
        </span>
    </button>
</div>
        </form>
    </div>

    <!-- Gespeicherte Abrechnungen -->
    <div class="bg-white shadow rounded-lg p-6">
        <h5 class="text-lg font-medium text-gray-800">Gespeicherte Abrechnungen</h5>

        <!-- Filter & Suche -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
<x-form.input
    label="Mieter suchen"
    id="searchTerm"
    name="searchTerm"
    type="text"
    wire:model.live="searchTerm"
    placeholder="Mieter suchen..."
/>

<x-form.date
    label="Von"
    id="fromDate"
    name="fromDate"
    wire:model.change="fromDate"
/>

<x-form.date
    label="Bis"
    id="toDate"
    name="toDate"
    wire:model.change="toDate"
/>
            <button wire:click="resetFilters"
                class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">
                Reset
            </button>
        </div>

        <!-- Tabelle -->
        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('billing_header_creator_name')"
                            class="px-4 py-2 text-left font-medium text-gray-600 cursor-pointer">
                            Ersteller</th>
                        <th wire:click="sortBy('tenant_first_name')"
                            class="px-4 py-2 text-left font-medium text-gray-600 cursor-pointer">
                            Mieter</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Zeitraum</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">PDF Seite 1</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">PDF Seite 2</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">
                            Nebenkostenzahlungen</th>
                        <th wire:click="sortBy('billing_records.created_at')"
                            class="px-4 py-2 text-left font-medium text-gray-600 cursor-pointer">
                            Erstellungsdatum</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($savedBillings as $billing)
                        <tr>
                            <td class="px-4 py-2">

    {{-- Ersteller (kommt aus billing_headers.creator_name) --}}
{{ $billing->billingHeader->creator_name ?? 'N/A' }}</td>


                            <td class="px-4 py-2">{{ $billing->tenant->first_name ?? "N/A" }}
                                {{ $billing->tenant->last_name ?? "N/A" }}</td>
                            <td class="px-4 py-2">{{ $billing->billing_period ?? "N/A" }}</td>
                            <td class="px-4 py-2"><a href="{{ $billing->pdf_path }}"
                                    target="_blank" class="text-pink-600 hover:underline">Seite 1
                                    anzeigen</a></td>
                            <td class="px-4 py-2"><a href="{{ $billing->pdf_path_second }}"
                                    target="_blank" class="text-pink-600 hover:underline">Seite 2
                                    anzeigen</a></td>
                            <td class="px-4 py-2"><a href="{{ $billing->pdf_path_third }}"
                                    target="_blank" class="text-pink-600 hover:underline">Seite 3
                                    anzeigen</a></td>
                            <td class="px-4 py-2">
                                {{ \Carbon\Carbon::parse($billing->created_at)->format("d.m.Y H:i") }}
                            </td>
                            <td class="px-4 py-2 flex gap-2">
                                <button wire:click="editBilling({{ $billing->id }})"
                                    class="px-2 py-1 bg-blue-500 text-white text-xs rounded-md hover:bg-blue-600">
                                    Bearbeiten
                                </button>
                                <button wire:click="deleteBilling({{ $billing->id }})"
                                    onclick="return confirm('Sind Sie sicher?')"
                                    class="px-2 py-1 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">
                                    Löschen
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="mt-4">
            {{-- {{ $savedBillings->links() }} --}}
        </div>
    </div>
</div>
