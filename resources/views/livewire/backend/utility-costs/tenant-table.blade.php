<div class="p-6">
    <!-- Kopfbereich -->
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Mieter verwalten</h2>
        <p class="mt-2 text-sm text-gray-600">
            Hier können Sie neue Mieter anlegen, bestehende bearbeiten oder löschen. Jeder Mieter
            kann einem Mietobjekt
            zugeordnet werden, inklusive Abrechnungstyp und Verbrauchszähler für Gas, Wasser und
            Strom.
        </p>
    </div>

    <!-- Formular -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h5 class="text-lg font-medium text-gray-800 mb-4">Mieter Formular</h5>

<form wire:submit.prevent="{{ $editMode ? 'updateTenant' : 'addTenant' }}" class="space-y-8">

    <!-- Allgemeine Informationen -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h6 class="text-sm font-semibold text-gray-700 mb-2">Allgemeine Informationen</h6>
            <div class="space-y-4">
                <x-form.input id="first_name" name="first_name" label="Vorname" wire:model="first_name" />
                <x-form.input id="last_name" name="last_name" label="Nachname" wire:model="last_name" />
                <x-form.input id="street" name="street" label="Straße" wire:model="street" />
                <x-form.input id="house_number" name="house_number" label="Hausnummer" wire:model="house_number" />
                <x-form.input id="zip_code" name="zip_code" label="Postleitzahl" wire:model="zip_code" />
                <x-form.input id="city" name="city" label="Stadt" wire:model="city" />
                <x-form.input id="phone" name="phone" label="Telefon" wire:model="phone" />
                <x-form.input id="email" name="email" type="email" label="E-Mail" wire:model="email" />
            </div>
        </div>

        <!-- Mietobjekt und Abrechnung -->
        <div>
            <h6 class="text-sm font-semibold text-gray-700 mb-2">Mietobjekt und Abrechnung</h6>
            <div class="space-y-4">
                <x-form.select id="rental_object_id" name="rental_object_id" label="Mietobjekt" wire:model="rental_object_id">
                    <option value="">Wählen...</option>
                    @foreach ($rentalObjects as $object)
                        <option value="{{ $object->id }}">
                            {{ $object->name }}, {{ $object->street }} {{ $object->house_number }}, {{ $object->city }}
                        </option>
                    @endforeach
                </x-form.select>

                <x-form.select id="billing_type" name="billing_type" label="Abrechnungstyp" wire:model.change="billing_type">
                    <option value="units">Einheiten</option>
                    <option value="people">Personen</option>
                    <option value="flat_rate">Nebenkostenpauschale</option>
                </x-form.select>

                <x-form.input
                    id="unit_count"
                    name="unit_count"
                    type="number"
                    label="Anzahl der Einheiten"
                    wire:model="unit_count"
                    :disabled="$billing_type !== 'units'" />

                <x-form.input
                    id="person_count"
                    name="person_count"
                    type="number"
                    label="Anzahl der Personen"
                    wire:model="person_count"
                    :disabled="$billing_type !== 'people'" />
            </div>
        </div>
    </div>

    <hr class="my-6">

    <!-- Mietdauer und Zähler -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h6 class="text-sm font-semibold text-gray-700 mb-2">Mietdauer und Einheiten</h6>
            <div class="space-y-4">
                <x-form.date id="start_date" name="start_date" label="Mietbeginn" wire:model="start_date" />
                <x-form.date id="end_date" name="end_date" label="Mietende (optional)" wire:model="end_date" />
                <x-form.input id="square_meters" name="square_meters" type="number" step="0.01" label="Quadratmeter" wire:model="square_meters" />
            </div>
        </div>

        <div>
            <h6 class="text-sm font-semibold text-gray-700 mb-2">Zählerstände</h6>
            <div class="space-y-4">
                <x-form.input id="gas_meter" name="gas_meter" type="number" step="0.01" label="Zählerstand Gas" wire:model="gas_meter" />
                <x-form.input id="electricity_meter" name="electricity_meter" type="number" step="0.01" label="Zählerstand Strom" wire:model="electricity_meter" />
                <x-form.input id="water_meter" name="water_meter" type="number" step="0.01" label="Zählerstand Wasser" wire:model="water_meter" />
                <x-form.input id="hot_water_meter" name="hot_water_meter" type="number" step="0.01" label="Zählerstand Warmwasser" wire:model="hot_water_meter" />
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-3 mt-6">
        <button type="submit"
            class="px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md shadow hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
            {{ $editMode ? "Mieter aktualisieren" : "Mieter hinzufügen" }}
        </button>
        @if ($editMode)
            <button type="button" wire:click="resetFields"
                class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md shadow hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                Abbrechen
            </button>
        @endif
    </div>
</form>

    </div>

    <!-- Mieterübersicht -->
    <div class="bg-white shadow rounded-lg p-6">
        <h5 class="text-lg font-medium text-gray-800 mb-4">Mieterübersicht</h5>

        <!-- Tabelle: nur auf md+ sichtbar -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-[1200px] w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Vorname</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Nachname</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Telefon</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">E-Mail</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Adresse</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">m²</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Gas</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Strom</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Wasser</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Warmwasser</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Zeitraum</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Abrechnung</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Anzahl</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($tenants as $tenant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap">{{ $tenant->first_name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $tenant->last_name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $tenant->phone }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $tenant->email }}</td>
                            <td class="px-4 py-2">
                                @if ($tenant->rentalObject)
                                    {{ $tenant->rentalObject->name }},
                                    {{ $tenant->rentalObject->street }}
                                    {{ $tenant->rentalObject->house_number }},
                                    {{ $tenant->rentalObject->zip_code }}
                                    {{ $tenant->rentalObject->city }}
                                @else
                                    <em class="text-gray-400">Nicht zugewiesen</em>
                                @endif
                            </td>
                            <td class="px-4 py-2">{{ $tenant->square_meters ?? "N/A" }}</td>
                            <td class="px-4 py-2">{{ $tenant->gas_meter ?? "N/A" }}</td>
                            <td class="px-4 py-2">{{ $tenant->electricity_meter ?? "N/A" }}</td>
                            <td class="px-4 py-2">{{ $tenant->water_meter ?? "N/A" }}</td>
                            <td class="px-4 py-2">{{ $tenant->hot_water_meter ?? "N/A" }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                {{ $tenant->start_date ? \Carbon\Carbon::parse($tenant->start_date)->format("d.m.Y") : "-" }}
                                –
                                {{ $tenant->end_date ? \Carbon\Carbon::parse($tenant->end_date)->format("d.m.Y") : "Unbefristet" }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @if ($tenant->billing_type === "units")
                                    Einheiten
                                @elseif($tenant->billing_type === "people")
                                    Personen
                                @elseif($tenant->flat_rate)
                                    Pauschale
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if (!$tenant->flat_rate)
                                    {{ $tenant->billing_type === "units" ? $tenant->unit_count : $tenant->person_count }}
                                @else
                                    <em class="text-gray-400">-</em>
                                @endif
                            </td>
                            <td class="px-4 py-2 flex gap-2">
                                <button wire:click="editTenant({{ $tenant->id }})"
                                    class="px-2 py-1 bg-yellow-500 text-white text-xs rounded-md hover:bg-yellow-600">
                                    Bearbeiten
                                </button>
                                <button wire:click="deleteTenant({{ $tenant->id }})"
                                    onclick="return confirm('Möchten Sie diesen Mieter wirklich löschen?')"
                                    class="px-2 py-1 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">
                                    Löschen
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Cards: nur auf kleinen Screens sichtbar -->
        <div class="space-y-4 md:hidden">
            @foreach ($tenants as $tenant)
                <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-4 space-y-2">
                    <p><span class="font-medium">Name:</span> {{ $tenant->first_name }}
                        {{ $tenant->last_name }}</p>
                    <p><span class="font-medium">Telefon:</span> {{ $tenant->phone }}</p>
                    <p><span class="font-medium">E-Mail:</span> {{ $tenant->email }}</p>
                    <p><span class="font-medium">Adresse:</span>
                        @if ($tenant->rentalObject)
                            {{ $tenant->rentalObject->street }}
                            {{ $tenant->rentalObject->house_number }},
                            {{ $tenant->rentalObject->zip_code }}
                            {{ $tenant->rentalObject->city }}
                        @else
                            <em class="text-gray-400">Nicht zugewiesen</em>
                        @endif
                    </p>
                    <p><span class="font-medium">m²:</span> {{ $tenant->square_meters ?? "N/A" }}
                    </p>
                    <p><span class="font-medium">Gas:</span> {{ $tenant->gas_meter ?? "N/A" }}</p>
                    <p><span class="font-medium">Strom:</span>
                        {{ $tenant->electricity_meter ?? "N/A" }}</p>
                    <p><span class="font-medium">Wasser:</span>
                        {{ $tenant->water_meter ?? "N/A" }}</p>
                    <p><span class="font-medium">Warmwasser:</span>
                        {{ $tenant->hot_water_meter ?? "N/A" }}</p>
                    <p><span class="font-medium">Zeitraum:</span>
                        {{ $tenant->start_date ? \Carbon\Carbon::parse($tenant->start_date)->format("d.m.Y") : "-" }}
                        –
                        {{ $tenant->end_date ? \Carbon\Carbon::parse($tenant->end_date)->format("d.m.Y") : "Unbefristet" }}
                    </p>
                    <p><span class="font-medium">Abrechnung:</span>
                        @if ($tenant->billing_type === "units")
                            Einheiten
                        @elseif($tenant->billing_type === "people")
                            Personen
                        @elseif($tenant->flat_rate)
                            Pauschale
                        @endif
                    </p>
                    <p><span class="font-medium">Anzahl:</span>
                        @if (!$tenant->flat_rate)
                            {{ $tenant->billing_type === "units" ? $tenant->unit_count : $tenant->person_count }}
                        @else
                            <em class="text-gray-400">-</em>
                        @endif
                    </p>

                    <div class="flex gap-2 pt-2">
                        <button wire:click="editTenant({{ $tenant->id }})"
                            class="px-3 py-1 bg-yellow-500 text-white text-xs rounded-md hover:bg-yellow-600">
                            Bearbeiten
                        </button>
                        <button wire:click="deleteTenant({{ $tenant->id }})"
                            onclick="return confirm('Möchten Sie diesen Mieter wirklich löschen?')"
                            class="px-3 py-1 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">
                            Löschen
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
