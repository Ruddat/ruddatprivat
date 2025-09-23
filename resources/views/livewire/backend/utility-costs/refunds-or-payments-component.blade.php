<!-- Main content start -->
<div class="space-y-6">
    <!-- Formular -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b">
            <h5 class="text-lg font-semibold text-gray-800">
                {{ $editMode ? "Eintrag bearbeiten" : "Neuen Eintrag hinzufügen" }}
            </h5>
        </div>
        <div class="p-6">
<form wire:submit.prevent="saveEntry" class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Mieter -->
    <x-form.select id="tenant_id" name="tenant_id" label="Mieter" wire:model="tenant_id">
        <option value="">Bitte auswählen...</option>
        @foreach ($tenants as $tenant)
            <option value="{{ $tenant->id }}">
                {{ $tenant->first_name }} {{ $tenant->last_name }}
            </option>
        @endforeach
    </x-form.select>

    <!-- Mietobjekt -->
    <x-form.select id="rental_object_id" name="rental_object_id" label="Mietobjekt" wire:model="rental_object_id">
        <option value="">Bitte auswählen...</option>
        @foreach ($rentalObjects as $object)
            <option value="{{ $object->id }}">{{ $object->name }}</option>
        @endforeach
    </x-form.select>

    <!-- Typ -->
    <x-form.select id="type" name="type" label="Typ" wire:model="type">
        <option value="">Bitte wählen...</option>
        <option value="refund">Erstattung</option>
        <option value="payment">Zahlung</option>
    </x-form.select>

    <!-- Betrag -->
    <x-form.input
        id="amount"
        name="amount"
        type="number"
        step="0.01"
        label="Betrag (€)"
        wire:model="amount" />

    <!-- Datum -->
    <x-form.date
        id="payment_date"
        name="payment_date"
        label="Datum"
        wire:model="payment_date" />

    <!-- Notiz -->
    <x-form.textarea
        id="note"
        name="note"
        rows="3"
        label="Notiz"
        wire:model="note"
        class="md:col-span-3" />

    <!-- Buttons -->
    <div class="md:col-span-3 flex justify-end gap-3">
        <button type="submit"
            class="px-4 py-2 bg-pink-600 text-white rounded-md shadow hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
            {{ $editMode ? 'Aktualisieren' : 'Speichern' }}
        </button>
        <button type="button" wire:click="resetFields"
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md shadow hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
            Zurücksetzen
        </button>
    </div>
</form>

        </div>
    </div>

    <!-- Liste -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b">
            <h5 class="text-lg font-semibold text-gray-800">Liste der Einträge</h5>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Mieter</th>
                        <th class="px-4 py-2 text-left">Mietobjekt</th>
                        <th class="px-4 py-2 text-left">Typ</th>
                        <th class="px-4 py-2 text-left">Betrag (€)</th>
                        <th class="px-4 py-2 text-left">Datum</th>
                        <th class="px-4 py-2 text-left">Notiz</th>
                        <th class="px-4 py-2 text-left">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($entries as $entry)
                        <tr>
                            <td class="px-4 py-2">{{ $entry->tenant->first_name }}</td>
                            <td class="px-4 py-2">{{ $entry->rentalObject->name }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                         {{ $entry->type === "refund" ? "bg-red-100 text-red-800" : "bg-green-100 text-green-800" }}">
                                    {{ ucfirst($entry->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ number_format($entry->amount, 2, ",", ".") }}
                            </td>
                            <td class="px-4 py-2">{{ $entry->payment_date }}</td>
                            <td class="px-4 py-2">{{ $entry->note }}</td>
                            <td class="px-4 py-2 flex gap-2">
                                <button wire:click="editEntry({{ $entry->id }})"
                                    class="px-2 py-1 bg-yellow-500 text-white text-xs rounded-md hover:bg-yellow-600">
                                    Bearbeiten
                                </button>
                                <button wire:click="deleteEntry({{ $entry->id }})"
                                    onclick="return confirm('Eintrag löschen?')"
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
</div>
<!-- Main content end -->
