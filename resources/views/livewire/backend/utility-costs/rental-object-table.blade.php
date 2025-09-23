<div class="space-y-6">
    <!-- Toggle Button für Formular -->
    <div>
        <button wire:click="toggleForm"
            class="px-4 py-2 bg-pink-600 text-white rounded shadow hover:bg-pink-700 transition">
            {{ $showForm ? "Formular ausblenden" : "Neues Objekt hinzufügen" }}
        </button>
    </div>

    <!-- Formular -->
    @if ($showForm)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h5 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                {{ $editMode ? "Mietobjekt bearbeiten" : "Neues Mietobjekt hinzufügen" }}
            </h5>

<form wire:submit.prevent="{{ $editMode ? 'updateRentalObject' : 'addRentalObject' }}" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Objektname -->
        <x-form.input
            id="name"
            name="name"
            label="Objektname"
            wire:model="name" />

        <!-- Straße -->
        <x-form.input
            id="street"
            name="street"
            label="Straße"
            wire:model="street" />

        <!-- Hausnummer -->
        <x-form.input
            id="house_number"
            name="house_number"
            label="Hausnummer"
            wire:model="house_number" />

        <!-- Etage -->
        <x-form.input
            id="floor"
            name="floor"
            label="Etage"
            wire:model="floor" />

        <!-- PLZ -->
        <x-form.input
            id="zip_code"
            name="zip_code"
            label="PLZ"
            wire:model="zip_code" />

        <!-- Stadt -->
        <x-form.input
            id="city"
            name="city"
            label="Stadt"
            wire:model="city" />

        <!-- Land -->
        <x-form.input
            id="country"
            name="country"
            label="Land"
            wire:model="country" />

        <!-- Objekttyp -->
        <x-form.select id="object_type" name="object_type" label="Objekttyp" wire:model="object_type">
            <option value="">Wählen...</option>
            <option value="Gewerbe">Gewerbe</option>
            <option value="Privat">Privat</option>
            <option value="Garage">Garage</option>
        </x-form.select>

        <!-- Max. Einheiten -->
        <x-form.input
            id="max_units"
            name="max_units"
            label="Max. Einheiten"
            type="number"
            wire:model="max_units" />

        <!-- Quadratmeter -->
        <x-form.input
            id="square_meters"
            name="square_meters"
            label="Quadratmeter"
            type="number"
            step="0.01"
            min="0"
            wire:model="square_meters" />

        <!-- Heiztyp -->
        <x-form.select id="heating_type" name="heating_type" label="Heiztyp" wire:model="heating_type">
            <option value="">Wählen...</option>
            <option value="Gas">Gas</option>
            <option value="Öl">Öl</option>
            <option value="Fernwärme">Fernwärme</option>
            <option value="Elektro">Elektro</option>
        </x-form.select>
    </div>

    <!-- Beschreibung -->
    <x-form.textarea
        id="description"
        name="description"
        label="Beschreibung"
        wire:model="description" />

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-2">
        <button type="submit"
            class="px-4 py-2 bg-pink-600 text-white rounded shadow hover:bg-pink-700 transition">
            {{ $editMode ? "Mietobjekt aktualisieren" : "Mietobjekt hinzufügen" }}
        </button>
        @if ($editMode)
            <button type="button" wire:click="resetFields"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded shadow hover:bg-gray-400 transition">
                Abbrechen
            </button>
        @endif
    </div>
</form>
        </div>
    @endif

    <!-- Tabelle -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h5 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Mietobjekte Liste
            </h5>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">
                            Objektname</th>
                        <th
                            class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">
                            Straße</th>
                        <th class="px-4 py-2">Hausnr.</th>
                        <th class="px-4 py-2">Etage</th>
                        <th class="px-4 py-2">PLZ</th>
                        <th class="px-4 py-2">Stadt</th>
                        <th class="px-4 py-2">Land</th>
                        <th class="px-4 py-2">Typ</th>
                        <th class="px-4 py-2">Einheiten</th>
                        <th class="px-4 py-2">qm</th>
                        <th class="px-4 py-2">Heizung</th>
                        <th class="px-4 py-2">Beschreibung</th>
                        <th class="px-4 py-2">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($rentalObjects as $object)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2">{{ $object->name }}</td>
                            <td class="px-4 py-2">{{ $object->street }}</td>
                            <td class="px-4 py-2">{{ $object->house_number }}</td>
                            <td class="px-4 py-2">{{ $object->floor }}</td>
                            <td class="px-4 py-2">{{ $object->zip_code }}</td>
                            <td class="px-4 py-2">{{ $object->city }}</td>
                            <td class="px-4 py-2">{{ $object->country }}</td>
                            <td class="px-4 py-2">{{ $object->object_type }}</td>
                            <td class="px-4 py-2">{{ $object->max_units }}</td>
                            <td class="px-4 py-2">{{ $object->square_meters }}</td>
                            <td class="px-4 py-2">{{ $object->heating_type }}</td>
                            <td class="px-4 py-2">{{ $object->description }}</td>
                            <td class="px-4 py-2 space-x-2">
                                <button wire:click="editRentalObject({{ $object->id }})"
                                    class="px-3 py-1 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition">
                                    ✏️
                                </button>
                                <button wire:click="deleteRentalObject({{ $object->id }})"
                                    onclick="return confirm('Möchten Sie dieses Mietobjekt wirklich löschen?')"
                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
