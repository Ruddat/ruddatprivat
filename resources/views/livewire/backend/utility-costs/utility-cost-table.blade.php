<div class="p-6">
    <!-- Kopfbereich -->
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Nebenkostenpositionen verwalten</h2>
        <p class="mt-2 text-sm text-gray-600">
            Hier können Sie alle umlagefähigen Nebenkostenpositionen wie Heizung, Wasser,
            Müllentsorgung oder Versicherungen
            anlegen, bearbeiten und löschen. Diese Positionen fließen später in die Abrechnungen
            ein.
        </p>
    </div>

    <!-- Formular -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h5 class="text-lg font-medium text-gray-800 mb-4">
            {{ $editMode ? "Position bearbeiten" : "Neue Position hinzufügen" }}
        </h5>

<form wire:submit.prevent="{{ $editMode ? 'updateUtilityCost' : 'addUtilityCost' }}" class="space-y-6">

    <!-- Name -->
    <x-form.input
        label="Name der Position"
        id="name"
        name="name"
        type="text"
        wire:model="name"
        required
    />

    <!-- Beschreibung -->
    <x-form.textarea
        label="Beschreibung"
        id="description"
        name="description"
        rows="3"
        wire:model="description"
    />

    <!-- Betrag -->
    <x-form.input
        label="Betrag (€)"
        id="amount"
        name="amount"
        type="number"
        step="0.01"
        wire:model="amount"
        required
    />

    <!-- Verteilerschlüssel -->
    <x-form.select
        label="Verteilerschlüssel"
        id="distribution_key"
        wire:model="distribution_key"
        required
        :error="$errors->first('distribution_key')"
    >
        <option value="units">Einheiten</option>
        <option value="people">Personenanzahl</option>
        <option value="area">Wohnfläche</option>
        <option value="consumption">Nach Verbrauch</option>
    </x-form.select>

    <!-- Buttons -->
    <div class="flex gap-3">
        <button type="submit"
            class="inline-flex items-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md shadow hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
            {{ $editMode ? 'Position aktualisieren' : 'Position hinzufügen' }}
        </button>

        @if ($editMode)
            <button type="button" wire:click="resetFields"
                class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md shadow hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                Abbrechen
            </button>
        @endif
    </div>
</form>

    </div>

    <!-- Tabelle -->
    <div class="bg-white shadow rounded-lg p-6 overflow-x-auto">
        <h5 class="text-lg font-medium text-gray-800 mb-4">Nebenkostenpositionen</h5>

        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Name</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600 hidden md:table-cell">
                        Beschreibung</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Betrag (€)</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Verteilerschlüssel
                    </th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($utilityCosts as $cost)
                    <tr>
                        <td class="px-4 py-2">{{ $cost->name }}</td>
                        <td class="px-4 py-2 hidden md:table-cell">
                            {!! nl2br(e(wordwrap($cost->description, 50, "\n"))) !!}
                        </td>
                        <td class="px-4 py-2">{{ number_format($cost->amount, 2, ",", ".") }}</td>
                        <td class="px-4 py-2">
                            @if ($cost->distribution_key == "units")
                                Einheiten
                            @elseif ($cost->distribution_key == "people")
                                Personenanzahl
                            @elseif ($cost->distribution_key == "area")
                                Wohnfläche
                            @elseif ($cost->distribution_key == "consumption")
                                Nach Verbrauch
                            @endif
                        </td>
                        <td class="px-4 py-2 flex gap-2">
                            <button wire:click="editUtilityCost({{ $cost->id }})"
                                class="px-2 py-1 bg-yellow-500 text-white text-xs rounded-md hover:bg-yellow-600">
                                Bearbeiten
                            </button>
                            <button wire:click="deleteUtilityCost({{ $cost->id }})"
                                onclick="return confirm('Möchten Sie diese Position wirklich löschen?')"
                                class="px-2 py-1 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">
                                Löschen
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
