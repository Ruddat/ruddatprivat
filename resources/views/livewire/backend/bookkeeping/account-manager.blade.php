<div class="space-y-6">
    <!-- Neues Konto -->
    <form wire:submit.prevent="save"
        class="bg-white p-4 rounded shadow grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="text" wire:model="number" class="form-input" placeholder="Nummer">
        <input type="text" wire:model="name" class="form-input" placeholder="Bezeichnung">
        <select wire:model="type" class="form-select">
            <option value="asset">Aktiva</option>
            <option value="liability">Passiva</option>
            <option value="equity">Eigenkapital</option>
            <option value="revenue">Erlöse</option>
            <option value="expense">Aufwand</option>
        </select>
        <button type="submit" class="bg-pink-600 text-white px-3 py-1 rounded">
            {{ $accountId ? "Aktualisieren" : "Anlegen" }}
        </button>
    </form>

    <!-- Kontorahmen importieren -->
    <div class="bg-white p-4 rounded shadow">
        <label class="form-label">Kontorahmen hinzufügen</label>
        <div class="flex gap-2">
            <select wire:model="framework" class="form-select">
                @foreach ($frameworks as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <button wire:click="importFramework"
                class="bg-gray-200 px-3 py-1 rounded hover:bg-gray-300">
                Importieren
            </button>
        </div>
    </div>

    <!-- Tabelle -->
    <div class="bg-white p-4 rounded shadow">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th>Nr</th>
                    <th>Name</th>
                    <th>Typ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accounts as $a)
                    <tr class="border-b">
                        <td>{{ $a->number }}</td>
                        <td>{{ $a->name }}</td>
                        <td>{{ ucfirst($a->type) }}</td>
                        <td>
                            <button wire:click="edit({{ $a->id }})"
                                class="text-blue-600">Bearbeiten</button>
                            <button wire:click="delete({{ $a->id }})"
                                class="text-red-600 ml-2">Löschen</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
